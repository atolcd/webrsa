/*  Prototype JavaScript framework, version 1.6.1
 *  (c) 2005-2009 Sam Stephenson
 *
 *  Prototype is freely distributable under the terms of an MIT-style license.
 *  For details, see the Prototype web site: http://www.prototypejs.org/
 *
 *--------------------------------------------------------------------------*/

var Prototype = {
  Version: '1.6.1',

  Browser: (function(){
    var ua = navigator.userAgent;
    var isOpera = Object.prototype.toString.call(window.opera) == '[object Opera]';
    return {
      IE:             !!window.attachEvent && !isOpera,
      Opera:          isOpera,
      WebKit:         ua.indexOf('AppleWebKit/') > -1,
      Gecko:          ua.indexOf('Gecko') > -1 && ua.indexOf('KHTML') === -1,
      MobileSafari:   /Apple.*Mobile.*Safari/.test(ua)
    }
  })(),

  BrowserFeatures: {
    XPath: !!document.evaluate,
    SelectorsAPI: !!document.querySelector,
    ElementExtensions: (function() {
      var constructor = window.Element || window.HTMLElement;
      return !!(constructor && constructor.prototype);
    })(),
    SpecificElementExtensions: (function() {
      if (typeof window.HTMLDivElement !== 'undefined')
        return true;

      var div = document.createElement('div');
      var form = document.createElement('form');
      var isSupported = false;

      if (div['__proto__'] && (div['__proto__'] !== form['__proto__'])) {
        isSupported = true;
      }

      div = form = null;

      return isSupported;
    })()
  },

  ScriptFragment: '<script[^>]*>([\\S\\s]*?)<\/script>',
  JSONFilter: /^\/\*-secure-([\s\S]*)\*\/\s*$/,

  emptyFunction: function() { },
  K: function(x) { return x }
};

if (Prototype.Browser.MobileSafari)
  Prototype.BrowserFeatures.SpecificElementExtensions = false;


var Abstract = { };


var Try = {
  these: function() {
    var returnValue;

    for (var i = 0, length = arguments.length; i < length; i++) {
      var lambda = arguments[i];
      try {
        returnValue = lambda();
        break;
      } catch (e) { }
    }

    return returnValue;
  }
};

/* Based on Alex Arnell's inheritance implementation. */

var Class = (function() {
  function subclass() {};
  function create() {
    var parent = null, properties = $A(arguments);
    if (Object.isFunction(properties[0]))
      parent = properties.shift();

    function klass() {
      this.initialize.apply(this, arguments);
    }

    Object.extend(klass, Class.Methods);
    klass.superclass = parent;
    klass.subclasses = [];

    if (parent) {
      subclass.prototype = parent.prototype;
      klass.prototype = new subclass;
      parent.subclasses.push(klass);
    }

    for (var i = 0; i < properties.length; i++)
      klass.addMethods(properties[i]);

    if (!klass.prototype.initialize)
      klass.prototype.initialize = Prototype.emptyFunction;

    klass.prototype.constructor = klass;
    return klass;
  }

  function addMethods(source) {
    var ancestor   = this.superclass && this.superclass.prototype;
    var properties = Object.keys(source);

    if (!Object.keys({ toString: true }).length) {
      if (source.toString != Object.prototype.toString)
        properties.push("toString");
      if (source.valueOf != Object.prototype.valueOf)
        properties.push("valueOf");
    }

    for (var i = 0, length = properties.length; i < length; i++) {
      var property = properties[i], value = source[property];
      if (ancestor && Object.isFunction(value) &&
          value.argumentNames().first() == "$super") {
        var method = value;
        value = (function(m) {
          return function() { return ancestor[m].apply(this, arguments); };
        })(property).wrap(method);

        value.valueOf = method.valueOf.bind(method);
        value.toString = method.toString.bind(method);
      }
      this.prototype[property] = value;
    }

    return this;
  }

  return {
    create: create,
    Methods: {
      addMethods: addMethods
    }
  };
})();
(function() {

  var _toString = Object.prototype.toString;

  function extend(destination, source) {
    for (var property in source)
      destination[property] = source[property];
    return destination;
  }

  function inspect(object) {
    try {
      if (isUndefined(object)) return 'undefined';
      if (object === null) return 'null';
      return object.inspect ? object.inspect() : String(object);
    } catch (e) {
      if (e instanceof RangeError) return '...';
      throw e;
    }
  }

  function toJSON(object) {
    var type = typeof object;
    switch (type) {
      case 'undefined':
      case 'function':
      case 'unknown': return;
      case 'boolean': return object.toString();
    }

    if (object === null) return 'null';
    if (object.toJSON) return object.toJSON();
    if (isElement(object)) return;

    var results = [];
    for (var property in object) {
      var value = toJSON(object[property]);
      if (!isUndefined(value))
        results.push(property.toJSON() + ': ' + value);
    }

    return '{' + results.join(', ') + '}';
  }

  function toQueryString(object) {
    return $H(object).toQueryString();
  }

  function toHTML(object) {
    return object && object.toHTML ? object.toHTML() : String.interpret(object);
  }

  function keys(object) {
    var results = [];
    for (var property in object)
      results.push(property);
    return results;
  }

  function values(object) {
    var results = [];
    for (var property in object)
      results.push(object[property]);
    return results;
  }

  function clone(object) {
    return extend({ }, object);
  }

  function isElement(object) {
    return !!(object && object.nodeType == 1);
  }

  function isArray(object) {
    return _toString.call(object) == "[object Array]";
  }


  function isHash(object) {
    return object instanceof Hash;
  }

  function isFunction(object) {
    return typeof object === "function";
  }

  function isString(object) {
    return _toString.call(object) == "[object String]";
  }

  function isNumber(object) {
    return _toString.call(object) == "[object Number]";
  }

  function isUndefined(object) {
    return typeof object === "undefined";
  }

  extend(Object, {
    extend:        extend,
    inspect:       inspect,
    toJSON:        toJSON,
    toQueryString: toQueryString,
    toHTML:        toHTML,
    keys:          keys,
    values:        values,
    clone:         clone,
    isElement:     isElement,
    isArray:       isArray,
    isHash:        isHash,
    isFunction:    isFunction,
    isString:      isString,
    isNumber:      isNumber,
    isUndefined:   isUndefined
  });
})();
Object.extend(Function.prototype, (function() {
  var slice = Array.prototype.slice;

  function update(array, args) {
    var arrayLength = array.length, length = args.length;
    while (length--) array[arrayLength + length] = args[length];
    return array;
  }

  function merge(array, args) {
    array = slice.call(array, 0);
    return update(array, args);
  }

  function argumentNames() {
    var names = this.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
      .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
      .replace(/\s+/g, '').split(',');
    return names.length == 1 && !names[0] ? [] : names;
  }

  function bind(context) {
    if (arguments.length < 2 && Object.isUndefined(arguments[0])) return this;
    var __method = this, args = slice.call(arguments, 1);
    return function() {
      var a = merge(args, arguments);
      return __method.apply(context, a);
    }
  }

  function bindAsEventListener(context) {
    var __method = this, args = slice.call(arguments, 1);
    return function(event) {
      var a = update([event || window.event], args);
      return __method.apply(context, a);
    }
  }

  function curry() {
    if (!arguments.length) return this;
    var __method = this, args = slice.call(arguments, 0);
    return function() {
      var a = merge(args, arguments);
      return __method.apply(this, a);
    }
  }

  function delay(timeout) {
    var __method = this, args = slice.call(arguments, 1);
    timeout = timeout * 1000
    return window.setTimeout(function() {
      return __method.apply(__method, args);
    }, timeout);
  }

  function defer() {
    var args = update([0.01], arguments);
    return this.delay.apply(this, args);
  }

  function wrap(wrapper) {
    var __method = this;
    return function() {
      var a = update([__method.bind(this)], arguments);
      return wrapper.apply(this, a);
    }
  }

  function methodize() {
    if (this._methodized) return this._methodized;
    var __method = this;
    return this._methodized = function() {
      var a = update([this], arguments);
      return __method.apply(null, a);
    };
  }

  return {
    argumentNames:       argumentNames,
    bind:                bind,
    bindAsEventListener: bindAsEventListener,
    curry:               curry,
    delay:               delay,
    defer:               defer,
    wrap:                wrap,
    methodize:           methodize
  }
})());


Date.prototype.toJSON = function() {
  return '"' + this.getUTCFullYear() + '-' +
    (this.getUTCMonth() + 1).toPaddedString(2) + '-' +
    this.getUTCDate().toPaddedString(2) + 'T' +
    this.getUTCHours().toPaddedString(2) + ':' +
    this.getUTCMinutes().toPaddedString(2) + ':' +
    this.getUTCSeconds().toPaddedString(2) + 'Z"';
};


RegExp.prototype.match = RegExp.prototype.test;

RegExp.escape = function(str) {
  return String(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
};
var PeriodicalExecuter = Class.create({
  initialize: function(callback, frequency) {
    this.callback = callback;
    this.frequency = frequency;
    this.currentlyExecuting = false;

    this.registerCallback();
  },

  registerCallback: function() {
    this.timer = setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
  },

  execute: function() {
    this.callback(this);
  },

  stop: function() {
    if (!this.timer) return;
    clearInterval(this.timer);
    this.timer = null;
  },

  onTimerEvent: function() {
    if (!this.currentlyExecuting) {
      try {
        this.currentlyExecuting = true;
        this.execute();
        this.currentlyExecuting = false;
      } catch(e) {
        this.currentlyExecuting = false;
        throw e;
      }
    }
  }
});
Object.extend(String, {
  interpret: function(value) {
    return value == null ? '' : String(value);
  },
  specialChar: {
    '\b': '\\b',
    '\t': '\\t',
    '\n': '\\n',
    '\f': '\\f',
    '\r': '\\r',
    '\\': '\\\\'
  }
});

Object.extend(String.prototype, (function() {

  function prepareReplacement(replacement) {
    if (Object.isFunction(replacement)) return replacement;
    var template = new Template(replacement);
    return function(match) { return template.evaluate(match) };
  }

  function gsub(pattern, replacement) {
    var result = '', source = this, match;
    replacement = prepareReplacement(replacement);

    if (Object.isString(pattern))
      pattern = RegExp.escape(pattern);

    if (!(pattern.length || pattern.source)) {
      replacement = replacement('');
      return replacement + source.split('').join(replacement) + replacement;
    }

    while (source.length > 0) {
      if (match = source.match(pattern)) {
        result += source.slice(0, match.index);
        result += String.interpret(replacement(match));
        source  = source.slice(match.index + match[0].length);
      } else {
        result += source, source = '';
      }
    }
    return result;
  }

  function sub(pattern, replacement, count) {
    replacement = prepareReplacement(replacement);
    count = Object.isUndefined(count) ? 1 : count;

    return this.gsub(pattern, function(match) {
      if (--count < 0) return match[0];
      return replacement(match);
    });
  }

  function scan(pattern, iterator) {
    this.gsub(pattern, iterator);
    return String(this);
  }

  function truncate(length, truncation) {
    length = length || 30;
    truncation = Object.isUndefined(truncation) ? '...' : truncation;
    return this.length > length ?
      this.slice(0, length - truncation.length) + truncation : String(this);
  }

  function strip() {
    return this.replace(/^\s+/, '').replace(/\s+$/, '');
  }

  function stripTags() {
    return this.replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
  }

  function stripScripts() {
    return this.replace(new RegExp(Prototype.ScriptFragment, 'img'), '');
  }

  function extractScripts() {
    var matchAll = new RegExp(Prototype.ScriptFragment, 'img');
    var matchOne = new RegExp(Prototype.ScriptFragment, 'im');
    return (this.match(matchAll) || []).map(function(scriptTag) {
      return (scriptTag.match(matchOne) || ['', ''])[1];
    });
  }

  function evalScripts() {
    return this.extractScripts().map(function(script) { return eval(script) });
  }

  function escapeHTML() {
    return this.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function unescapeHTML() {
    return this.stripTags().replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
  }


  function toQueryParams(separator) {
    var match = this.strip().match(/([^?#]*)(#.*)?$/);
    if (!match) return { };

    return match[1].split(separator || '&').inject({ }, function(hash, pair) {
      if ((pair = pair.split('='))[0]) {
        var key = decodeURIComponent(pair.shift());
        var value = pair.length > 1 ? pair.join('=') : pair[0];
        if (value != undefined) value = decodeURIComponent(value);

        if (key in hash) {
          if (!Object.isArray(hash[key])) hash[key] = [hash[key]];
          hash[key].push(value);
        }
        else hash[key] = value;
      }
      return hash;
    });
  }

  function toArray() {
    return this.split('');
  }

  function succ() {
    return this.slice(0, this.length - 1) +
      String.fromCharCode(this.charCodeAt(this.length - 1) + 1);
  }

  function times(count) {
    return count < 1 ? '' : new Array(count + 1).join(this);
  }

  function camelize() {
    var parts = this.split('-'), len = parts.length;
    if (len == 1) return parts[0];

    var camelized = this.charAt(0) == '-'
      ? parts[0].charAt(0).toUpperCase() + parts[0].substring(1)
      : parts[0];

    for (var i = 1; i < len; i++)
      camelized += parts[i].charAt(0).toUpperCase() + parts[i].substring(1);

    return camelized;
  }

  function capitalize() {
    return this.charAt(0).toUpperCase() + this.substring(1).toLowerCase();
  }

  function underscore() {
    return this.replace(/::/g, '/')
               .replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
               .replace(/([a-z\d])([A-Z])/g, '$1_$2')
               .replace(/-/g, '_')
               .toLowerCase();
  }

  function dasherize() {
    return this.replace(/_/g, '-');
  }

  function inspect(useDoubleQuotes) {
    var escapedString = this.replace(/[\x00-\x1f\\]/g, function(character) {
      if (character in String.specialChar) {
        return String.specialChar[character];
      }
      return '\\u00' + character.charCodeAt().toPaddedString(2, 16);
    });
    if (useDoubleQuotes) return '"' + escapedString.replace(/"/g, '\\"') + '"';
    return "'" + escapedString.replace(/'/g, '\\\'') + "'";
  }

  function toJSON() {
    return this.inspect(true);
  }

  function unfilterJSON(filter) {
    return this.replace(filter || Prototype.JSONFilter, '$1');
  }

  function isJSON() {
    var str = this;
    if (str.blank()) return false;
    str = this.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, '');
    return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);
  }

  function evalJSON(sanitize) {
    var json = this.unfilterJSON();
    try {
      if (!sanitize || json.isJSON()) return eval('(' + json + ')');
    } catch (e) { }
    throw new SyntaxError('Badly formed JSON string: ' + this.inspect());
  }

  function include(pattern) {
    return this.indexOf(pattern) > -1;
  }

  function startsWith(pattern) {
    return this.indexOf(pattern) === 0;
  }

  function endsWith(pattern) {
    var d = this.length - pattern.length;
    return d >= 0 && this.lastIndexOf(pattern) === d;
  }

  function empty() {
    return this == '';
  }

  function blank() {
    return /^\s*$/.test(this);
  }

  function interpolate(object, pattern) {
    return new Template(this, pattern).evaluate(object);
  }

  return {
    gsub:           gsub,
    sub:            sub,
    scan:           scan,
    truncate:       truncate,
    strip:          String.prototype.trim ? String.prototype.trim : strip,
    stripTags:      stripTags,
    stripScripts:   stripScripts,
    extractScripts: extractScripts,
    evalScripts:    evalScripts,
    escapeHTML:     escapeHTML,
    unescapeHTML:   unescapeHTML,
    toQueryParams:  toQueryParams,
    parseQuery:     toQueryParams,
    toArray:        toArray,
    succ:           succ,
    times:          times,
    camelize:       camelize,
    capitalize:     capitalize,
    underscore:     underscore,
    dasherize:      dasherize,
    inspect:        inspect,
    toJSON:         toJSON,
    unfilterJSON:   unfilterJSON,
    isJSON:         isJSON,
    evalJSON:       evalJSON,
    include:        include,
    startsWith:     startsWith,
    endsWith:       endsWith,
    empty:          empty,
    blank:          blank,
    interpolate:    interpolate
  };
})());

var Template = Class.create({
  initialize: function(template, pattern) {
    this.template = template.toString();
    this.pattern = pattern || Template.Pattern;
  },

  evaluate: function(object) {
    if (object && Object.isFunction(object.toTemplateReplacements))
      object = object.toTemplateReplacements();

    return this.template.gsub(this.pattern, function(match) {
      if (object == null) return (match[1] + '');

      var before = match[1] || '';
      if (before == '\\') return match[2];

      var ctx = object, expr = match[3];
      var pattern = /^([^.[]+|\[((?:.*?[^\\])?)\])(\.|\[|$)/;
      match = pattern.exec(expr);
      if (match == null) return before;

      while (match != null) {
        var comp = match[1].startsWith('[') ? match[2].replace(/\\\\]/g, ']') : match[1];
        ctx = ctx[comp];
        if (null == ctx || '' == match[3]) break;
        expr = expr.substring('[' == match[3] ? match[1].length : match[0].length);
        match = pattern.exec(expr);
      }

      return before + String.interpret(ctx);
    });
  }
});
Template.Pattern = /(^|.|\r|\n)(#\{(.*?)\})/;

var $break = { };

var Enumerable = (function() {
  function each(iterator, context) {
    var index = 0;
    try {
      this._each(function(value) {
        iterator.call(context, value, index++);
      });
    } catch (e) {
      if (e != $break) throw e;
    }
    return this;
  }

  function eachSlice(number, iterator, context) {
    var index = -number, slices = [], array = this.toArray();
    if (number < 1) return array;
    while ((index += number) < array.length)
      slices.push(array.slice(index, index+number));
    return slices.collect(iterator, context);
  }

  function all(iterator, context) {
    iterator = iterator || Prototype.K;
    var result = true;
    this.each(function(value, index) {
      result = result && !!iterator.call(context, value, index);
      if (!result) throw $break;
    });
    return result;
  }

  function any(iterator, context) {
    iterator = iterator || Prototype.K;
    var result = false;
    this.each(function(value, index) {
      if (result = !!iterator.call(context, value, index))
        throw $break;
    });
    return result;
  }

  function collect(iterator, context) {
    iterator = iterator || Prototype.K;
    var results = [];
    this.each(function(value, index) {
      results.push(iterator.call(context, value, index));
    });
    return results;
  }

  function detect(iterator, context) {
    var result;
    this.each(function(value, index) {
      if (iterator.call(context, value, index)) {
        result = value;
        throw $break;
      }
    });
    return result;
  }

  function findAll(iterator, context) {
    var results = [];
    this.each(function(value, index) {
      if (iterator.call(context, value, index))
        results.push(value);
    });
    return results;
  }

  function grep(filter, iterator, context) {
    iterator = iterator || Prototype.K;
    var results = [];

    if (Object.isString(filter))
      filter = new RegExp(RegExp.escape(filter));

    this.each(function(value, index) {
      if (filter.match(value))
        results.push(iterator.call(context, value, index));
    });
    return results;
  }

  function include(object) {
    if (Object.isFunction(this.indexOf))
      if (this.indexOf(object) != -1) return true;

    var found = false;
    this.each(function(value) {
      if (value == object) {
        found = true;
        throw $break;
      }
    });
    return found;
  }

  function inGroupsOf(number, fillWith) {
    fillWith = Object.isUndefined(fillWith) ? null : fillWith;
    return this.eachSlice(number, function(slice) {
      while(slice.length < number) slice.push(fillWith);
      return slice;
    });
  }

  function inject(memo, iterator, context) {
    this.each(function(value, index) {
      memo = iterator.call(context, memo, value, index);
    });
    return memo;
  }

  function invoke(method) {
    var args = $A(arguments).slice(1);
    return this.map(function(value) {
      return value[method].apply(value, args);
    });
  }

  function max(iterator, context) {
    iterator = iterator || Prototype.K;
    var result;
    this.each(function(value, index) {
      value = iterator.call(context, value, index);
      if (result == null || value >= result)
        result = value;
    });
    return result;
  }

  function min(iterator, context) {
    iterator = iterator || Prototype.K;
    var result;
    this.each(function(value, index) {
      value = iterator.call(context, value, index);
      if (result == null || value < result)
        result = value;
    });
    return result;
  }

  function partition(iterator, context) {
    iterator = iterator || Prototype.K;
    var trues = [], falses = [];
    this.each(function(value, index) {
      (iterator.call(context, value, index) ?
        trues : falses).push(value);
    });
    return [trues, falses];
  }

  function pluck(property) {
    var results = [];
    this.each(function(value) {
      results.push(value[property]);
    });
    return results;
  }

  function reject(iterator, context) {
    var results = [];
    this.each(function(value, index) {
      if (!iterator.call(context, value, index))
        results.push(value);
    });
    return results;
  }

  function sortBy(iterator, context) {
    return this.map(function(value, index) {
      return {
        value: value,
        criteria: iterator.call(context, value, index)
      };
    }).sort(function(left, right) {
      var a = left.criteria, b = right.criteria;
      return a < b ? -1 : a > b ? 1 : 0;
    }).pluck('value');
  }

  function toArray() {
    return this.map();
  }

  function zip() {
    var iterator = Prototype.K, args = $A(arguments);
    if (Object.isFunction(args.last()))
      iterator = args.pop();

    var collections = [this].concat(args).map($A);
    return this.map(function(value, index) {
      return iterator(collections.pluck(index));
    });
  }

  function size() {
    return this.toArray().length;
  }

  function inspect() {
    return '#<Enumerable:' + this.toArray().inspect() + '>';
  }









  return {
    each:       each,
    eachSlice:  eachSlice,
    all:        all,
    every:      all,
    any:        any,
    some:       any,
    collect:    collect,
    map:        collect,
    detect:     detect,
    findAll:    findAll,
    select:     findAll,
    filter:     findAll,
    grep:       grep,
    include:    include,
    member:     include,
    inGroupsOf: inGroupsOf,
    inject:     inject,
    invoke:     invoke,
    max:        max,
    min:        min,
    partition:  partition,
    pluck:      pluck,
    reject:     reject,
    sortBy:     sortBy,
    toArray:    toArray,
    entries:    toArray,
    zip:        zip,
    size:       size,
    inspect:    inspect,
    find:       detect
  };
})();
function $A(iterable) {
  if (!iterable) return [];
  if ('toArray' in Object(iterable)) return iterable.toArray();
  var length = iterable.length || 0, results = new Array(length);
  while (length--) results[length] = iterable[length];
  return results;
}

function $w(string) {
  if (!Object.isString(string)) return [];
  string = string.strip();
  return string ? string.split(/\s+/) : [];
}

Array.from = $A;


(function() {
  var arrayProto = Array.prototype,
      slice = arrayProto.slice,
      _each = arrayProto.forEach; // use native browser JS 1.6 implementation if available

  function each(iterator) {
    for (var i = 0, length = this.length; i < length; i++)
      iterator(this[i]);
  }
  if (!_each) _each = each;

  function clear() {
    this.length = 0;
    return this;
  }

  function first() {
    return this[0];
  }

  function last() {
    return this[this.length - 1];
  }

  function compact() {
    return this.select(function(value) {
      return value != null;
    });
  }

  function flatten() {
    return this.inject([], function(array, value) {
      if (Object.isArray(value))
        return array.concat(value.flatten());
      array.push(value);
      return array;
    });
  }

  function without() {
    var values = slice.call(arguments, 0);
    return this.select(function(value) {
      return !values.include(value);
    });
  }

  function reverse(inline) {
    return (inline !== false ? this : this.toArray())._reverse();
  }

  function uniq(sorted) {
    return this.inject([], function(array, value, index) {
      if (0 == index || (sorted ? array.last() != value : !array.include(value)))
        array.push(value);
      return array;
    });
  }

  function intersect(array) {
    return this.uniq().findAll(function(item) {
      return array.detect(function(value) { return item === value });
    });
  }


  function clone() {
    return slice.call(this, 0);
  }

  function size() {
    return this.length;
  }

  function inspect() {
    return '[' + this.map(Object.inspect).join(', ') + ']';
  }

  function toJSON() {
    var results = [];
    this.each(function(object) {
      var value = Object.toJSON(object);
      if (!Object.isUndefined(value)) results.push(value);
    });
    return '[' + results.join(', ') + ']';
  }

  function indexOf(item, i) {
    i || (i = 0);
    var length = this.length;
    if (i < 0) i = length + i;
    for (; i < length; i++)
      if (this[i] === item) return i;
    return -1;
  }

  function lastIndexOf(item, i) {
    i = isNaN(i) ? this.length : (i < 0 ? this.length + i : i) + 1;
    var n = this.slice(0, i).reverse().indexOf(item);
    return (n < 0) ? n : i - n - 1;
  }

  function concat() {
    var array = slice.call(this, 0), item;
    for (var i = 0, length = arguments.length; i < length; i++) {
      item = arguments[i];
      if (Object.isArray(item) && !('callee' in item)) {
        for (var j = 0, arrayLength = item.length; j < arrayLength; j++)
          array.push(item[j]);
      } else {
        array.push(item);
      }
    }
    return array;
  }

  Object.extend(arrayProto, Enumerable);

  if (!arrayProto._reverse)
    arrayProto._reverse = arrayProto.reverse;

  Object.extend(arrayProto, {
    _each:     _each,
    clear:     clear,
    first:     first,
    last:      last,
    compact:   compact,
    flatten:   flatten,
    without:   without,
    reverse:   reverse,
    uniq:      uniq,
    intersect: intersect,
    clone:     clone,
    toArray:   clone,
    size:      size,
    inspect:   inspect,
    toJSON:    toJSON
  });

  var CONCAT_ARGUMENTS_BUGGY = (function() {
    return [].concat(arguments)[0][0] !== 1;
  })(1,2)

  if (CONCAT_ARGUMENTS_BUGGY) arrayProto.concat = concat;

  if (!arrayProto.indexOf) arrayProto.indexOf = indexOf;
  if (!arrayProto.lastIndexOf) arrayProto.lastIndexOf = lastIndexOf;
})();
function $H(object) {
  return new Hash(object);
};

var Hash = Class.create(Enumerable, (function() {
  function initialize(object) {
    this._object = Object.isHash(object) ? object.toObject() : Object.clone(object);
  }

  function _each(iterator) {
    for (var key in this._object) {
      var value = this._object[key], pair = [key, value];
      pair.key = key;
      pair.value = value;
      iterator(pair);
    }
  }

  function set(key, value) {
    return this._object[key] = value;
  }

  function get(key) {
    if (this._object[key] !== Object.prototype[key])
      return this._object[key];
  }

  function unset(key) {
    var value = this._object[key];
    delete this._object[key];
    return value;
  }

  function toObject() {
    return Object.clone(this._object);
  }

  function keys() {
    return this.pluck('key');
  }

  function values() {
    return this.pluck('value');
  }

  function index(value) {
    var match = this.detect(function(pair) {
      return pair.value === value;
    });
    return match && match.key;
  }

  function merge(object) {
    return this.clone().update(object);
  }

  function update(object) {
    return new Hash(object).inject(this, function(result, pair) {
      result.set(pair.key, pair.value);
      return result;
    });
  }

  function toQueryPair(key, value) {
    if (Object.isUndefined(value)) return key;
    return key + '=' + encodeURIComponent(String.interpret(value));
  }

  function toQueryString() {
    return this.inject([], function(results, pair) {
      var key = encodeURIComponent(pair.key), values = pair.value;

      if (values && typeof values == 'object') {
        if (Object.isArray(values))
          return results.concat(values.map(toQueryPair.curry(key)));
      } else results.push(toQueryPair(key, values));
      return results;
    }).join('&');
  }

  function inspect() {
    return '#<Hash:{' + this.map(function(pair) {
      return pair.map(Object.inspect).join(': ');
    }).join(', ') + '}>';
  }

  function toJSON() {
    return Object.toJSON(this.toObject());
  }

  function clone() {
    return new Hash(this);
  }

  return {
    initialize:             initialize,
    _each:                  _each,
    set:                    set,
    get:                    get,
    unset:                  unset,
    toObject:               toObject,
    toTemplateReplacements: toObject,
    keys:                   keys,
    values:                 values,
    index:                  index,
    merge:                  merge,
    update:                 update,
    toQueryString:          toQueryString,
    inspect:                inspect,
    toJSON:                 toJSON,
    clone:                  clone
  };
})());

Hash.from = $H;
Object.extend(Number.prototype, (function() {
  function toColorPart() {
    return this.toPaddedString(2, 16);
  }

  function succ() {
    return this + 1;
  }

  function times(iterator, context) {
    $R(0, this, true).each(iterator, context);
    return this;
  }

  function toPaddedString(length, radix) {
    var string = this.toString(radix || 10);
    return '0'.times(length - string.length) + string;
  }

  function toJSON() {
    return isFinite(this) ? this.toString() : 'null';
  }

  function abs() {
    return Math.abs(this);
  }

  function round() {
    return Math.round(this);
  }

  function ceil() {
    return Math.ceil(this);
  }

  function floor() {
    return Math.floor(this);
  }

  return {
    toColorPart:    toColorPart,
    succ:           succ,
    times:          times,
    toPaddedString: toPaddedString,
    toJSON:         toJSON,
    abs:            abs,
    round:          round,
    ceil:           ceil,
    floor:          floor
  };
})());

function $R(start, end, exclusive) {
  return new ObjectRange(start, end, exclusive);
}

var ObjectRange = Class.create(Enumerable, (function() {
  function initialize(start, end, exclusive) {
    this.start = start;
    this.end = end;
    this.exclusive = exclusive;
  }

  function _each(iterator) {
    var value = this.start;
    while (this.include(value)) {
      iterator(value);
      value = value.succ();
    }
  }

  function include(value) {
    if (value < this.start)
      return false;
    if (this.exclusive)
      return value < this.end;
    return value <= this.end;
  }

  return {
    initialize: initialize,
    _each:      _each,
    include:    include
  };
})());



var Ajax = {
  getTransport: function() {
    return Try.these(
      function() {return new XMLHttpRequest()},
      function() {return new ActiveXObject('Msxml2.XMLHTTP')},
      function() {return new ActiveXObject('Microsoft.XMLHTTP')}
    ) || false;
  },

  activeRequestCount: 0
};

Ajax.Responders = {
  responders: [],

  _each: function(iterator) {
    this.responders._each(iterator);
  },

  register: function(responder) {
    if (!this.include(responder))
      this.responders.push(responder);
  },

  unregister: function(responder) {
    this.responders = this.responders.without(responder);
  },

  dispatch: function(callback, request, transport, json) {
    this.each(function(responder) {
      if (Object.isFunction(responder[callback])) {
        try {
          responder[callback].apply(responder, [request, transport, json]);
        } catch (e) { }
      }
    });
  }
};

Object.extend(Ajax.Responders, Enumerable);

Ajax.Responders.register({
  onCreate:   function() { Ajax.activeRequestCount++ },
  onComplete: function() { Ajax.activeRequestCount-- }
});
Ajax.Base = Class.create({
  initialize: function(options) {
    this.options = {
      method:       'post',
      asynchronous: true,
      contentType:  'application/x-www-form-urlencoded',
      encoding:     'UTF-8',
      parameters:   '',
      evalJSON:     true,
      evalJS:       true
    };
    Object.extend(this.options, options || { });

    this.options.method = this.options.method.toLowerCase();

    if (Object.isString(this.options.parameters))
      this.options.parameters = this.options.parameters.toQueryParams();
    else if (Object.isHash(this.options.parameters))
      this.options.parameters = this.options.parameters.toObject();
  }
});
Ajax.Request = Class.create(Ajax.Base, {
  _complete: false,

  initialize: function($super, url, options) {
    $super(options);
    this.transport = Ajax.getTransport();
    this.request(url);
  },

  request: function(url) {
    this.url = url;
    this.method = this.options.method;
    var params = Object.clone(this.options.parameters);

    if (!['get', 'post'].include(this.method)) {
      params['_method'] = this.method;
      this.method = 'post';
    }

    this.parameters = params;

    if (params = Object.toQueryString(params)) {
      if (this.method == 'get')
        this.url += (this.url.include('?') ? '&' : '?') + params;
      else if (/Konqueror|Safari|KHTML/.test(navigator.userAgent))
        params += '&_=';
    }

    try {
      var response = new Ajax.Response(this);
      if (this.options.onCreate) this.options.onCreate(response);
      Ajax.Responders.dispatch('onCreate', this, response);

      this.transport.open(this.method.toUpperCase(), this.url,
        this.options.asynchronous);

      if (this.options.asynchronous) this.respondToReadyState.bind(this).defer(1);

      this.transport.onreadystatechange = this.onStateChange.bind(this);
      this.setRequestHeaders();

      this.body = this.method == 'post' ? (this.options.postBody || params) : null;
      this.transport.send(this.body);

      /* Force Firefox to handle ready state 4 for synchronous requests */
      if (!this.options.asynchronous && this.transport.overrideMimeType)
        this.onStateChange();

    }
    catch (e) {
      this.dispatchException(e);
    }
  },

  onStateChange: function() {
    var readyState = this.transport.readyState;
    if (readyState > 1 && !((readyState == 4) && this._complete))
      this.respondToReadyState(this.transport.readyState);
  },

  setRequestHeaders: function() {
    var headers = {
      'X-Requested-With': 'XMLHttpRequest',
      'X-Prototype-Version': Prototype.Version,
      'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
    };

    if (this.method == 'post') {
      headers['Content-type'] = this.options.contentType +
        (this.options.encoding ? '; charset=' + this.options.encoding : '');

      /* Force "Connection: close" for older Mozilla browsers to work
       * around a bug where XMLHttpRequest sends an incorrect
       * Content-length header. See Mozilla Bugzilla #246651.
       */
      if (this.transport.overrideMimeType &&
          (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005)
            headers['Connection'] = 'close';
    }

    if (typeof this.options.requestHeaders == 'object') {
      var extras = this.options.requestHeaders;

      if (Object.isFunction(extras.push))
        for (var i = 0, length = extras.length; i < length; i += 2)
          headers[extras[i]] = extras[i+1];
      else
        $H(extras).each(function(pair) { headers[pair.key] = pair.value });
    }

    for (var name in headers)
      this.transport.setRequestHeader(name, headers[name]);
  },

  success: function() {
    var status = this.getStatus();
    return !status || (status >= 200 && status < 300);
  },

  getStatus: function() {
    try {
      return this.transport.status || 0;
    } catch (e) { return 0 }
  },

  respondToReadyState: function(readyState) {
    var state = Ajax.Request.Events[readyState], response = new Ajax.Response(this);

    if (state == 'Complete') {
      try {
        this._complete = true;
        (this.options['on' + response.status]
         || this.options['on' + (this.success() ? 'Success' : 'Failure')]
         || Prototype.emptyFunction)(response, response.headerJSON);
      } catch (e) {
        this.dispatchException(e);
      }

      var contentType = response.getHeader('Content-type');
      if (this.options.evalJS == 'force'
          || (this.options.evalJS && this.isSameOrigin() && contentType
          && contentType.match(/^\s*(text|application)\/(x-)?(java|ecma)script(;.*)?\s*$/i)))
        this.evalResponse();
    }

    try {
      (this.options['on' + state] || Prototype.emptyFunction)(response, response.headerJSON);
      Ajax.Responders.dispatch('on' + state, this, response, response.headerJSON);
    } catch (e) {
      this.dispatchException(e);
    }

    if (state == 'Complete') {
      this.transport.onreadystatechange = Prototype.emptyFunction;
    }
  },

  isSameOrigin: function() {
    var m = this.url.match(/^\s*https?:\/\/[^\/]*/);
    return !m || (m[0] == '#{protocol}//#{domain}#{port}'.interpolate({
      protocol: location.protocol,
      domain: document.domain,
      port: location.port ? ':' + location.port : ''
    }));
  },

  getHeader: function(name) {
    try {
      return this.transport.getResponseHeader(name) || null;
    } catch (e) { return null; }
  },

  evalResponse: function() {
    try {
      return eval((this.transport.responseText || '').unfilterJSON());
    } catch (e) {
      this.dispatchException(e);
    }
  },

  dispatchException: function(exception) {
    (this.options.onException || Prototype.emptyFunction)(this, exception);
    Ajax.Responders.dispatch('onException', this, exception);
  }
});

Ajax.Request.Events =
  ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'];








Ajax.Response = Class.create({
  initialize: function(request){
    this.request = request;
    var transport  = this.transport  = request.transport,
        readyState = this.readyState = transport.readyState;

    if((readyState > 2 && !Prototype.Browser.IE) || readyState == 4) {
      this.status       = this.getStatus();
      this.statusText   = this.getStatusText();
      this.responseText = String.interpret(transport.responseText);
      this.headerJSON   = this._getHeaderJSON();
    }

    if(readyState == 4) {
      var xml = transport.responseXML;
      this.responseXML  = Object.isUndefined(xml) ? null : xml;
      this.responseJSON = this._getResponseJSON();
    }
  },

  status:      0,

  statusText: '',

  getStatus: Ajax.Request.prototype.getStatus,

  getStatusText: function() {
    try {
      return this.transport.statusText || '';
    } catch (e) { return '' }
  },

  getHeader: Ajax.Request.prototype.getHeader,

  getAllHeaders: function() {
    try {
      return this.getAllResponseHeaders();
    } catch (e) { return null }
  },

  getResponseHeader: function(name) {
    return this.transport.getResponseHeader(name);
  },

  getAllResponseHeaders: function() {
    return this.transport.getAllResponseHeaders();
  },

  _getHeaderJSON: function() {
    var json = this.getHeader('X-JSON');
    if (!json) return null;
    json = decodeURIComponent(escape(json));
    try {
      return json.evalJSON(this.request.options.sanitizeJSON ||
        !this.request.isSameOrigin());
    } catch (e) {
      this.request.dispatchException(e);
    }
  },

  _getResponseJSON: function() {
    var options = this.request.options;
    if (!options.evalJSON || (options.evalJSON != 'force' &&
      !(this.getHeader('Content-type') || '').include('application/json')) ||
        this.responseText.blank())
          return null;
    try {
      return this.responseText.evalJSON(options.sanitizeJSON ||
        !this.request.isSameOrigin());
    } catch (e) {
      this.request.dispatchException(e);
    }
  }
});

Ajax.Updater = Class.create(Ajax.Request, {
  initialize: function($super, container, url, options) {
    this.container = {
      success: (container.success || container),
      failure: (container.failure || (container.success ? null : container))
    };

    options = Object.clone(options);
    var onComplete = options.onComplete;
    options.onComplete = (function(response, json) {
      this.updateContent(response.responseText);
      if (Object.isFunction(onComplete)) onComplete(response, json);
    }).bind(this);

    $super(url, options);
  },

  updateContent: function(responseText) {
    var receiver = this.container[this.success() ? 'success' : 'failure'],
        options = this.options;

    if (!options.evalScripts) responseText = responseText.stripScripts();

    if (receiver = $(receiver)) {
      if (options.insertion) {
        if (Object.isString(options.insertion)) {
          var insertion = { }; insertion[options.insertion] = responseText;
          receiver.insert(insertion);
        }
        else options.insertion(receiver, responseText);
      }
      else receiver.update(responseText);
    }
  }
});

Ajax.PeriodicalUpdater = Class.create(Ajax.Base, {
  initialize: function($super, container, url, options) {
    $super(options);
    this.onComplete = this.options.onComplete;

    this.frequency = (this.options.frequency || 2);
    this.decay = (this.options.decay || 1);

    this.updater = { };
    this.container = container;
    this.url = url;

    this.start();
  },

  start: function() {
    this.options.onComplete = this.updateComplete.bind(this);
    this.onTimerEvent();
  },

  stop: function() {
    this.updater.options.onComplete = undefined;
    clearTimeout(this.timer);
    (this.onComplete || Prototype.emptyFunction).apply(this, arguments);
  },

  updateComplete: function(response) {
    if (this.options.decay) {
      this.decay = (response.responseText == this.lastText ?
        this.decay * this.options.decay : 1);

      this.lastText = response.responseText;
    }
    this.timer = this.onTimerEvent.bind(this).delay(this.decay * this.frequency);
  },

  onTimerEvent: function() {
    this.updater = new Ajax.Updater(this.container, this.url, this.options);
  }
});



function $(element) {
  if (arguments.length > 1) {
    for (var i = 0, elements = [], length = arguments.length; i < length; i++)
      elements.push($(arguments[i]));
    return elements;
  }
  if (Object.isString(element))
    element = document.getElementById(element);
  return Element.extend(element);
}

if (Prototype.BrowserFeatures.XPath) {
  document._getElementsByXPath = function(expression, parentElement) {
    var results = [];
    var query = document.evaluate(expression, $(parentElement) || document,
      null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    for (var i = 0, length = query.snapshotLength; i < length; i++)
      results.push(Element.extend(query.snapshotItem(i)));
    return results;
  };
}

/*--------------------------------------------------------------------------*/

if (!window.Node) var Node = { };

if (!Node.ELEMENT_NODE) {
  Object.extend(Node, {
    ELEMENT_NODE: 1,
    ATTRIBUTE_NODE: 2,
    TEXT_NODE: 3,
    CDATA_SECTION_NODE: 4,
    ENTITY_REFERENCE_NODE: 5,
    ENTITY_NODE: 6,
    PROCESSING_INSTRUCTION_NODE: 7,
    COMMENT_NODE: 8,
    DOCUMENT_NODE: 9,
    DOCUMENT_TYPE_NODE: 10,
    DOCUMENT_FRAGMENT_NODE: 11,
    NOTATION_NODE: 12
  });
}


(function(global) {

  var SETATTRIBUTE_IGNORES_NAME = (function(){
    var elForm = document.createElement("form");
    var elInput = document.createElement("input");
    var root = document.documentElement;
    elInput.setAttribute("name", "test");
    elForm.appendChild(elInput);
    root.appendChild(elForm);
    var isBuggy = elForm.elements
      ? (typeof elForm.elements.test == "undefined")
      : null;
    root.removeChild(elForm);
    elForm = elInput = null;
    return isBuggy;
  })();

  var element = global.Element;
  global.Element = function(tagName, attributes) {
    attributes = attributes || { };
    tagName = tagName.toLowerCase();
    var cache = Element.cache;
    if (SETATTRIBUTE_IGNORES_NAME && attributes.name) {
      tagName = '<' + tagName + ' name="' + attributes.name + '">';
      delete attributes.name;
      return Element.writeAttribute(document.createElement(tagName), attributes);
    }
    if (!cache[tagName]) cache[tagName] = Element.extend(document.createElement(tagName));
    return Element.writeAttribute(cache[tagName].cloneNode(false), attributes);
  };
  Object.extend(global.Element, element || { });
  if (element) global.Element.prototype = element.prototype;
})(this);

Element.cache = { };
Element.idCounter = 1;

Element.Methods = {
  visible: function(element) {
    return $(element).style.display != 'none';
  },

  toggle: function(element) {
    element = $(element);
    Element[Element.visible(element) ? 'hide' : 'show'](element);
    return element;
  },


  hide: function(element) {
    element = $(element);
    element.style.display = 'none';
    return element;
  },

  show: function(element) {
    element = $(element);
    element.style.display = '';
    return element;
  },

  remove: function(element) {
    element = $(element);
    element.parentNode.removeChild(element);
    return element;
  },

  update: (function(){

    var SELECT_ELEMENT_INNERHTML_BUGGY = (function(){
      var el = document.createElement("select"),
          isBuggy = true;
      el.innerHTML = "<option value=\"test\">test</option>";
      if (el.options && el.options[0]) {
        isBuggy = el.options[0].nodeName.toUpperCase() !== "OPTION";
      }
      el = null;
      return isBuggy;
    })();

    var TABLE_ELEMENT_INNERHTML_BUGGY = (function(){
      try {
        var el = document.createElement("table");
        if (el && el.tBodies) {
          el.innerHTML = "<tbody><tr><td>test</td></tr></tbody>";
          var isBuggy = typeof el.tBodies[0] == "undefined";
          el = null;
          return isBuggy;
        }
      } catch (e) {
        return true;
      }
    })();

    var SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING = (function () {
      var s = document.createElement("script"),
          isBuggy = false;
      try {
        s.appendChild(document.createTextNode(""));
        isBuggy = !s.firstChild ||
          s.firstChild && s.firstChild.nodeType !== 3;
      } catch (e) {
        isBuggy = true;
      }
      s = null;
      return isBuggy;
    })();

    function update(element, content) {
      element = $(element);

      if (content && content.toElement)
        content = content.toElement();

      if (Object.isElement(content))
        return element.update().insert(content);

      content = Object.toHTML(content);

      var tagName = element.tagName.toUpperCase();

      if (tagName === 'SCRIPT' && SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING) {
        element.text = content;
        return element;
      }

      if (SELECT_ELEMENT_INNERHTML_BUGGY || TABLE_ELEMENT_INNERHTML_BUGGY) {
        if (tagName in Element._insertionTranslations.tags) {
          while (element.firstChild) {
            element.removeChild(element.firstChild);
          }
          Element._getContentFromAnonymousElement(tagName, content.stripScripts())
            .each(function(node) {
              element.appendChild(node)
            });
        }
        else {
          element.innerHTML = content.stripScripts();
        }
      }
      else {
        element.innerHTML = content.stripScripts();
      }

      content.evalScripts.bind(content).defer();
      return element;
    }

    return update;
  })(),

  replace: function(element, content) {
    element = $(element);
    if (content && content.toElement) content = content.toElement();
    else if (!Object.isElement(content)) {
      content = Object.toHTML(content);
      var range = element.ownerDocument.createRange();
      range.selectNode(element);
      content.evalScripts.bind(content).defer();
      content = range.createContextualFragment(content.stripScripts());
    }
    element.parentNode.replaceChild(content, element);
    return element;
  },

  insert: function(element, insertions) {
    element = $(element);

    if (Object.isString(insertions) || Object.isNumber(insertions) ||
        Object.isElement(insertions) || (insertions && (insertions.toElement || insertions.toHTML)))
          insertions = {bottom:insertions};

    var content, insert, tagName, childNodes;

    for (var position in insertions) {
      content  = insertions[position];
      position = position.toLowerCase();
      insert = Element._insertionTranslations[position];

      if (content && content.toElement) content = content.toElement();
      if (Object.isElement(content)) {
        insert(element, content);
        continue;
      }

      content = Object.toHTML(content);

      tagName = ((position == 'before' || position == 'after')
        ? element.parentNode : element).tagName.toUpperCase();

      childNodes = Element._getContentFromAnonymousElement(tagName, content.stripScripts());

      if (position == 'top' || position == 'after') childNodes.reverse();
      childNodes.each(insert.curry(element));

      content.evalScripts.bind(content).defer();
    }

    return element;
  },

  wrap: function(element, wrapper, attributes) {
    element = $(element);
    if (Object.isElement(wrapper))
      $(wrapper).writeAttribute(attributes || { });
    else if (Object.isString(wrapper)) wrapper = new Element(wrapper, attributes);
    else wrapper = new Element('div', wrapper);
    if (element.parentNode)
      element.parentNode.replaceChild(wrapper, element);
    wrapper.appendChild(element);
    return wrapper;
  },

  inspect: function(element) {
    element = $(element);
    var result = '<' + element.tagName.toLowerCase();
    $H({'id': 'id', 'className': 'class'}).each(function(pair) {
      var property = pair.first(), attribute = pair.last();
      var value = (element[property] || '').toString();
      if (value) result += ' ' + attribute + '=' + value.inspect(true);
    });
    return result + '>';
  },

  recursivelyCollect: function(element, property) {
    element = $(element);
    var elements = [];
    while (element = element[property])
      if (element.nodeType == 1)
        elements.push(Element.extend(element));
    return elements;
  },

  ancestors: function(element) {
    return Element.recursivelyCollect(element, 'parentNode');
  },

  descendants: function(element) {
    return Element.select(element, "*");
  },

  firstDescendant: function(element) {
    element = $(element).firstChild;
    while (element && element.nodeType != 1) element = element.nextSibling;
    return $(element);
  },

  immediateDescendants: function(element) {
    if (!(element = $(element).firstChild)) return [];
    while (element && element.nodeType != 1) element = element.nextSibling;
    if (element) return [element].concat($(element).nextSiblings());
    return [];
  },

  previousSiblings: function(element) {
    return Element.recursivelyCollect(element, 'previousSibling');
  },

  nextSiblings: function(element) {
    return Element.recursivelyCollect(element, 'nextSibling');
  },

  siblings: function(element) {
    element = $(element);
    return Element.previousSiblings(element).reverse()
      .concat(Element.nextSiblings(element));
  },

  match: function(element, selector) {
    if (Object.isString(selector))
      selector = new Selector(selector);
    return selector.match($(element));
  },

  up: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return $(element.parentNode);
    var ancestors = Element.ancestors(element);
    return Object.isNumber(expression) ? ancestors[expression] :
      Selector.findElement(ancestors, expression, index);
  },

  down: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return Element.firstDescendant(element);
    return Object.isNumber(expression) ? Element.descendants(element)[expression] :
      Element.select(element, expression)[index || 0];
  },

  previous: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return $(Selector.handlers.previousElementSibling(element));
    var previousSiblings = Element.previousSiblings(element);
    return Object.isNumber(expression) ? previousSiblings[expression] :
      Selector.findElement(previousSiblings, expression, index);
  },

  next: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return $(Selector.handlers.nextElementSibling(element));
    var nextSiblings = Element.nextSiblings(element);
    return Object.isNumber(expression) ? nextSiblings[expression] :
      Selector.findElement(nextSiblings, expression, index);
  },


  select: function(element) {
    var args = Array.prototype.slice.call(arguments, 1);
    return Selector.findChildElements(element, args);
  },

  adjacent: function(element) {
    var args = Array.prototype.slice.call(arguments, 1);
    return Selector.findChildElements(element.parentNode, args).without(element);
  },

  identify: function(element) {
    element = $(element);
    var id = Element.readAttribute(element, 'id');
    if (id) return id;
    do { id = 'anonymous_element_' + Element.idCounter++ } while ($(id));
    Element.writeAttribute(element, 'id', id);
    return id;
  },

  readAttribute: function(element, name) {
    element = $(element);
    if (Prototype.Browser.IE) {
      var t = Element._attributeTranslations.read;
      if (t.values[name]) return t.values[name](element, name);
      if (t.names[name]) name = t.names[name];
      if (name.include(':')) {
        return (!element.attributes || !element.attributes[name]) ? null :
         element.attributes[name].value;
      }
    }
    return element.getAttribute(name);
  },

  writeAttribute: function(element, name, value) {
    element = $(element);
    var attributes = { }, t = Element._attributeTranslations.write;

    if (typeof name == 'object') attributes = name;
    else attributes[name] = Object.isUndefined(value) ? true : value;

    for (var attr in attributes) {
      name = t.names[attr] || attr;
      value = attributes[attr];
      if (t.values[attr]) name = t.values[attr](element, value);
      if (value === false || value === null)
        element.removeAttribute(name);
      else if (value === true)
        element.setAttribute(name, name);
      else element.setAttribute(name, value);
    }
    return element;
  },

  getHeight: function(element) {
    return Element.getDimensions(element).height;
  },

  getWidth: function(element) {
    return Element.getDimensions(element).width;
  },

  classNames: function(element) {
    return new Element.ClassNames(element);
  },

  hasClassName: function(element, className) {
    if (!(element = $(element))) return;
    var elementClassName = element.className;
    return (elementClassName.length > 0 && (elementClassName == className ||
      new RegExp("(^|\\s)" + className + "(\\s|$)").test(elementClassName)));
  },

  addClassName: function(element, className) {
    if (!(element = $(element))) return;
    if (!Element.hasClassName(element, className))
      element.className += (element.className ? ' ' : '') + className;
    return element;
  },

  removeClassName: function(element, className) {
    if (!(element = $(element))) return;
    element.className = element.className.replace(
      new RegExp("(^|\\s+)" + className + "(\\s+|$)"), ' ').strip();
    return element;
  },

  toggleClassName: function(element, className) {
    if (!(element = $(element))) return;
    return Element[Element.hasClassName(element, className) ?
      'removeClassName' : 'addClassName'](element, className);
  },

  cleanWhitespace: function(element) {
    element = $(element);
    var node = element.firstChild;
    while (node) {
      var nextNode = node.nextSibling;
      if (node.nodeType == 3 && !/\S/.test(node.nodeValue))
        element.removeChild(node);
      node = nextNode;
    }
    return element;
  },

  empty: function(element) {
    return $(element).innerHTML.blank();
  },

  descendantOf: function(element, ancestor) {
    element = $(element), ancestor = $(ancestor);

    if (element.compareDocumentPosition)
      return (element.compareDocumentPosition(ancestor) & 8) === 8;

    if (ancestor.contains)
      return ancestor.contains(element) && ancestor !== element;

    while (element = element.parentNode)
      if (element == ancestor) return true;

    return false;
  },

  scrollTo: function(element) {
    element = $(element);
    var pos = Element.cumulativeOffset(element);
    window.scrollTo(pos[0], pos[1]);
    return element;
  },

  getStyle: function(element, style) {
    element = $(element);
    style = style == 'float' ? 'cssFloat' : style.camelize();
    var value = element.style[style];
    if (!value || value == 'auto') {
      var css = document.defaultView.getComputedStyle(element, null);
      value = css ? css[style] : null;
    }
    if (style == 'opacity') return value ? parseFloat(value) : 1.0;
    return value == 'auto' ? null : value;
  },

  getOpacity: function(element) {
    return $(element).getStyle('opacity');
  },

  setStyle: function(element, styles) {
    element = $(element);
    var elementStyle = element.style, match;
    if (Object.isString(styles)) {
      element.style.cssText += ';' + styles;
      return styles.include('opacity') ?
        element.setOpacity(styles.match(/opacity:\s*(\d?\.?\d*)/)[1]) : element;
    }
    for (var property in styles)
      if (property == 'opacity') element.setOpacity(styles[property]);
      else
        elementStyle[(property == 'float' || property == 'cssFloat') ?
          (Object.isUndefined(elementStyle.styleFloat) ? 'cssFloat' : 'styleFloat') :
            property] = styles[property];

    return element;
  },

  setOpacity: function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1 || value === '') ? '' :
      (value < 0.00001) ? 0 : value;
    return element;
  },

  getDimensions: function(element) {
    element = $(element);
    var display = Element.getStyle(element, 'display');
    if (display != 'none' && display != null) // Safari bug
      return {width: element.offsetWidth, height: element.offsetHeight};

    var els = element.style;
    var originalVisibility = els.visibility;
    var originalPosition = els.position;
    var originalDisplay = els.display;
    els.visibility = 'hidden';
    if (originalPosition != 'fixed') // Switching fixed to absolute causes issues in Safari
      els.position = 'absolute';
    els.display = 'block';
    var originalWidth = element.clientWidth;
    var originalHeight = element.clientHeight;
    els.display = originalDisplay;
    els.position = originalPosition;
    els.visibility = originalVisibility;
    return {width: originalWidth, height: originalHeight};
  },

  makePositioned: function(element) {
    element = $(element);
    var pos = Element.getStyle(element, 'position');
    if (pos == 'static' || !pos) {
      element._madePositioned = true;
      element.style.position = 'relative';
      if (Prototype.Browser.Opera) {
        element.style.top = 0;
        element.style.left = 0;
      }
    }
    return element;
  },

  undoPositioned: function(element) {
    element = $(element);
    if (element._madePositioned) {
      element._madePositioned = undefined;
      element.style.position =
        element.style.top =
        element.style.left =
        element.style.bottom =
        element.style.right = '';
    }
    return element;
  },

  makeClipping: function(element) {
    element = $(element);
    if (element._overflow) return element;
    element._overflow = Element.getStyle(element, 'overflow') || 'auto';
    if (element._overflow !== 'hidden')
      element.style.overflow = 'hidden';
    return element;
  },

  undoClipping: function(element) {
    element = $(element);
    if (!element._overflow) return element;
    element.style.overflow = element._overflow == 'auto' ? '' : element._overflow;
    element._overflow = null;
    return element;
  },

  cumulativeOffset: function(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;
      element = element.offsetParent;
    } while (element);
    return Element._returnOffset(valueL, valueT);
  },

  positionedOffset: function(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;
      element = element.offsetParent;
      if (element) {
        if (element.tagName.toUpperCase() == 'BODY') break;
        var p = Element.getStyle(element, 'position');
        if (p !== 'static') break;
      }
    } while (element);
    return Element._returnOffset(valueL, valueT);
  },

  absolutize: function(element) {
    element = $(element);
    if (Element.getStyle(element, 'position') == 'absolute') return element;

    var offsets = Element.positionedOffset(element);
    var top     = offsets[1];
    var left    = offsets[0];
    var width   = element.clientWidth;
    var height  = element.clientHeight;

    element._originalLeft   = left - parseFloat(element.style.left  || 0);
    element._originalTop    = top  - parseFloat(element.style.top || 0);
    element._originalWidth  = element.style.width;
    element._originalHeight = element.style.height;

    element.style.position = 'absolute';
    element.style.top    = top + 'px';
    element.style.left   = left + 'px';
    element.style.width  = width + 'px';
    element.style.height = height + 'px';
    return element;
  },

  relativize: function(element) {
    element = $(element);
    if (Element.getStyle(element, 'position') == 'relative') return element;

    element.style.position = 'relative';
    var top  = parseFloat(element.style.top  || 0) - (element._originalTop || 0);
    var left = parseFloat(element.style.left || 0) - (element._originalLeft || 0);

    element.style.top    = top + 'px';
    element.style.left   = left + 'px';
    element.style.height = element._originalHeight;
    element.style.width  = element._originalWidth;
    return element;
  },

  cumulativeScrollOffset: function(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.scrollTop  || 0;
      valueL += element.scrollLeft || 0;
      element = element.parentNode;
    } while (element);
    return Element._returnOffset(valueL, valueT);
  },

  getOffsetParent: function(element) {
    if (element.offsetParent) return $(element.offsetParent);
    if (element == document.body) return $(element);

    while ((element = element.parentNode) && element != document.body)
      if (Element.getStyle(element, 'position') != 'static')
        return $(element);

    return $(document.body);
  },

  viewportOffset: function(forElement) {
    var valueT = 0, valueL = 0;

    var element = forElement;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;

      if (element.offsetParent == document.body &&
        Element.getStyle(element, 'position') == 'absolute') break;

    } while (element = element.offsetParent);

    element = forElement;
    do {
      if (!Prototype.Browser.Opera || (element.tagName && (element.tagName.toUpperCase() == 'BODY'))) {
        valueT -= element.scrollTop  || 0;
        valueL -= element.scrollLeft || 0;
      }
    } while (element = element.parentNode);

    return Element._returnOffset(valueL, valueT);
  },

  clonePosition: function(element, source) {
    var options = Object.extend({
      setLeft:    true,
      setTop:     true,
      setWidth:   true,
      setHeight:  true,
      offsetTop:  0,
      offsetLeft: 0
    }, arguments[2] || { });

    source = $(source);
    var p = Element.viewportOffset(source);

    element = $(element);
    var delta = [0, 0];
    var parent = null;
    if (Element.getStyle(element, 'position') == 'absolute') {
      parent = Element.getOffsetParent(element);
      delta = Element.viewportOffset(parent);
    }

    if (parent == document.body) {
      delta[0] -= document.body.offsetLeft;
      delta[1] -= document.body.offsetTop;
    }

    if (options.setLeft)   element.style.left  = (p[0] - delta[0] + options.offsetLeft) + 'px';
    if (options.setTop)    element.style.top   = (p[1] - delta[1] + options.offsetTop) + 'px';
    if (options.setWidth)  element.style.width = source.offsetWidth + 'px';
    if (options.setHeight) element.style.height = source.offsetHeight + 'px';
    return element;
  }
};

Object.extend(Element.Methods, {
  getElementsBySelector: Element.Methods.select,

  childElements: Element.Methods.immediateDescendants
});

Element._attributeTranslations = {
  write: {
    names: {
      className: 'class',
      htmlFor:   'for'
    },
    values: { }
  }
};

if (Prototype.Browser.Opera) {
  Element.Methods.getStyle = Element.Methods.getStyle.wrap(
    function(proceed, element, style) {
      switch (style) {
        case 'left': case 'top': case 'right': case 'bottom':
          if (proceed(element, 'position') === 'static') return null;
        case 'height': case 'width':
          if (!Element.visible(element)) return null;

          var dim = parseInt(proceed(element, style), 10);

          if (dim !== element['offset' + style.capitalize()])
            return dim + 'px';

          var properties;
          if (style === 'height') {
            properties = ['border-top-width', 'padding-top',
             'padding-bottom', 'border-bottom-width'];
          }
          else {
            properties = ['border-left-width', 'padding-left',
             'padding-right', 'border-right-width'];
          }
          return properties.inject(dim, function(memo, property) {
            var val = proceed(element, property);
            return val === null ? memo : memo - parseInt(val, 10);
          }) + 'px';
        default: return proceed(element, style);
      }
    }
  );

  Element.Methods.readAttribute = Element.Methods.readAttribute.wrap(
    function(proceed, element, attribute) {
      if (attribute === 'title') return element.title;
      return proceed(element, attribute);
    }
  );
}

else if (Prototype.Browser.IE) {
  Element.Methods.getOffsetParent = Element.Methods.getOffsetParent.wrap(
    function(proceed, element) {
      element = $(element);
      try { element.offsetParent }
      catch(e) { return $(document.body) }
      var position = element.getStyle('position');
      if (position !== 'static') return proceed(element);
      element.setStyle({ position: 'relative' });
      var value = proceed(element);
      element.setStyle({ position: position });
      return value;
    }
  );

  $w('positionedOffset viewportOffset').each(function(method) {
    Element.Methods[method] = Element.Methods[method].wrap(
      function(proceed, element) {
        element = $(element);
        try { element.offsetParent }
        catch(e) { return Element._returnOffset(0,0) }
        var position = element.getStyle('position');
        if (position !== 'static') return proceed(element);
        var offsetParent = element.getOffsetParent();
        if (offsetParent && offsetParent.getStyle('position') === 'fixed')
          offsetParent.setStyle({ zoom: 1 });
        element.setStyle({ position: 'relative' });
        var value = proceed(element);
        element.setStyle({ position: position });
        return value;
      }
    );
  });

  Element.Methods.cumulativeOffset = Element.Methods.cumulativeOffset.wrap(
    function(proceed, element) {
      try { element.offsetParent }
      catch(e) { return Element._returnOffset(0,0) }
      return proceed(element);
    }
  );

  Element.Methods.getStyle = function(element, style) {
    element = $(element);
    style = (style == 'float' || style == 'cssFloat') ? 'styleFloat' : style.camelize();
    var value = element.style[style];
    if (!value && element.currentStyle) value = element.currentStyle[style];

    if (style == 'opacity') {
      if (value = (element.getStyle('filter') || '').match(/alpha\(opacity=(.*)\)/))
        if (value[1]) return parseFloat(value[1]) / 100;
      return 1.0;
    }

    if (value == 'auto') {
      if ((style == 'width' || style == 'height') && (element.getStyle('display') != 'none'))
        return element['offset' + style.capitalize()] + 'px';
      return null;
    }
    return value;
  };

  Element.Methods.setOpacity = function(element, value) {
    function stripAlpha(filter){
      return filter.replace(/alpha\([^\)]*\)/gi,'');
    }
    element = $(element);
    var currentStyle = element.currentStyle;
    if ((currentStyle && !currentStyle.hasLayout) ||
      (!currentStyle && element.style.zoom == 'normal'))
        element.style.zoom = 1;

    var filter = element.getStyle('filter'), style = element.style;
    if (value == 1 || value === '') {
      (filter = stripAlpha(filter)) ?
        style.filter = filter : style.removeAttribute('filter');
      return element;
    } else if (value < 0.00001) value = 0;
    style.filter = stripAlpha(filter) +
      'alpha(opacity=' + (value * 100) + ')';
    return element;
  };

  Element._attributeTranslations = (function(){

    var classProp = 'className';
    var forProp = 'for';

    var el = document.createElement('div');

    el.setAttribute(classProp, 'x');

    if (el.className !== 'x') {
      el.setAttribute('class', 'x');
      if (el.className === 'x') {
        classProp = 'class';
      }
    }
    el = null;

    el = document.createElement('label');
    el.setAttribute(forProp, 'x');
    if (el.htmlFor !== 'x') {
      el.setAttribute('htmlFor', 'x');
      if (el.htmlFor === 'x') {
        forProp = 'htmlFor';
      }
    }
    el = null;

    return {
      read: {
        names: {
          'class':      classProp,
          'className':  classProp,
          'for':        forProp,
          'htmlFor':    forProp
        },
        values: {
          _getAttr: function(element, attribute) {
            return element.getAttribute(attribute);
          },
          _getAttr2: function(element, attribute) {
            return element.getAttribute(attribute, 2);
          },
          _getAttrNode: function(element, attribute) {
            var node = element.getAttributeNode(attribute);
            return node ? node.value : "";
          },
          _getEv: (function(){

            var el = document.createElement('div');
            el.onclick = Prototype.emptyFunction;
            var value = el.getAttribute('onclick');
            var f;

            if (String(value).indexOf('{') > -1) {
              f = function(element, attribute) {
                attribute = element.getAttribute(attribute);
                if (!attribute) return null;
                attribute = attribute.toString();
                attribute = attribute.split('{')[1];
                attribute = attribute.split('}')[0];
                return attribute.strip();
              };
            }
            else if (value === '') {
              f = function(element, attribute) {
                attribute = element.getAttribute(attribute);
                if (!attribute) return null;
                return attribute.strip();
              };
            }
            el = null;
            return f;
          })(),
          _flag: function(element, attribute) {
            return $(element).hasAttribute(attribute) ? attribute : null;
          },
          style: function(element) {
            return element.style.cssText.toLowerCase();
          },
          title: function(element) {
            return element.title;
          }
        }
      }
    }
  })();

  Element._attributeTranslations.write = {
    names: Object.extend({
      cellpadding: 'cellPadding',
      cellspacing: 'cellSpacing'
    }, Element._attributeTranslations.read.names),
    values: {
      checked: function(element, value) {
        element.checked = !!value;
      },

      style: function(element, value) {
        element.style.cssText = value ? value : '';
      }
    }
  };

  Element._attributeTranslations.has = {};

  $w('colSpan rowSpan vAlign dateTime accessKey tabIndex ' +
      'encType maxLength readOnly longDesc frameBorder').each(function(attr) {
    Element._attributeTranslations.write.names[attr.toLowerCase()] = attr;
    Element._attributeTranslations.has[attr.toLowerCase()] = attr;
  });

  (function(v) {
    Object.extend(v, {
      href:        v._getAttr2,
      src:         v._getAttr2,
      type:        v._getAttr,
      action:      v._getAttrNode,
      disabled:    v._flag,
      checked:     v._flag,
      readonly:    v._flag,
      multiple:    v._flag,
      onload:      v._getEv,
      onunload:    v._getEv,
      onclick:     v._getEv,
      ondblclick:  v._getEv,
      onmousedown: v._getEv,
      onmouseup:   v._getEv,
      onmouseover: v._getEv,
      onmousemove: v._getEv,
      onmouseout:  v._getEv,
      onfocus:     v._getEv,
      onblur:      v._getEv,
      onkeypress:  v._getEv,
      onkeydown:   v._getEv,
      onkeyup:     v._getEv,
      onsubmit:    v._getEv,
      onreset:     v._getEv,
      onselect:    v._getEv,
      onchange:    v._getEv
    });
  })(Element._attributeTranslations.read.values);

  if (Prototype.BrowserFeatures.ElementExtensions) {
    (function() {
      function _descendants(element) {
        var nodes = element.getElementsByTagName('*'), results = [];
        for (var i = 0, node; node = nodes[i]; i++)
          if (node.tagName !== "!") // Filter out comment nodes.
            results.push(node);
        return results;
      }

      Element.Methods.down = function(element, expression, index) {
        element = $(element);
        if (arguments.length == 1) return element.firstDescendant();
        return Object.isNumber(expression) ? _descendants(element)[expression] :
          Element.select(element, expression)[index || 0];
      }
    })();
  }

}

else if (Prototype.Browser.Gecko && /rv:1\.8\.0/.test(navigator.userAgent)) {
  Element.Methods.setOpacity = function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1) ? 0.999999 :
      (value === '') ? '' : (value < 0.00001) ? 0 : value;
    return element;
  };
}

else if (Prototype.Browser.WebKit) {
  Element.Methods.setOpacity = function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1 || value === '') ? '' :
      (value < 0.00001) ? 0 : value;

    if (value == 1)
      if(element.tagName.toUpperCase() == 'IMG' && element.width) {
        element.width++; element.width--;
      } else try {
        var n = document.createTextNode(' ');
        element.appendChild(n);
        element.removeChild(n);
      } catch (e) { }

    return element;
  };

  Element.Methods.cumulativeOffset = function(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;
      if (element.offsetParent == document.body)
        if (Element.getStyle(element, 'position') == 'absolute') break;

      element = element.offsetParent;
    } while (element);

    return Element._returnOffset(valueL, valueT);
  };
}

if ('outerHTML' in document.documentElement) {
  Element.Methods.replace = function(element, content) {
    element = $(element);

    if (content && content.toElement) content = content.toElement();
    if (Object.isElement(content)) {
      element.parentNode.replaceChild(content, element);
      return element;
    }

    content = Object.toHTML(content);
    var parent = element.parentNode, tagName = parent.tagName.toUpperCase();

    if (Element._insertionTranslations.tags[tagName]) {
      var nextSibling = element.next();
      var fragments = Element._getContentFromAnonymousElement(tagName, content.stripScripts());
      parent.removeChild(element);
      if (nextSibling)
        fragments.each(function(node) { parent.insertBefore(node, nextSibling) });
      else
        fragments.each(function(node) { parent.appendChild(node) });
    }
    else element.outerHTML = content.stripScripts();

    content.evalScripts.bind(content).defer();
    return element;
  };
}

Element._returnOffset = function(l, t) {
  var result = [l, t];
  result.left = l;
  result.top = t;
  return result;
};

Element._getContentFromAnonymousElement = function(tagName, html) {
  var div = new Element('div'), t = Element._insertionTranslations.tags[tagName];
  if (t) {
    div.innerHTML = t[0] + html + t[1];
    t[2].times(function() { div = div.firstChild });
  } else div.innerHTML = html;
  return $A(div.childNodes);
};

Element._insertionTranslations = {
  before: function(element, node) {
    element.parentNode.insertBefore(node, element);
  },
  top: function(element, node) {
    element.insertBefore(node, element.firstChild);
  },
  bottom: function(element, node) {
    element.appendChild(node);
  },
  after: function(element, node) {
    element.parentNode.insertBefore(node, element.nextSibling);
  },
  tags: {
    TABLE:  ['<table>',                '</table>',                   1],
    TBODY:  ['<table><tbody>',         '</tbody></table>',           2],
    TR:     ['<table><tbody><tr>',     '</tr></tbody></table>',      3],
    TD:     ['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
    SELECT: ['<select>',               '</select>',                  1]
  }
};

(function() {
  var tags = Element._insertionTranslations.tags;
  Object.extend(tags, {
    THEAD: tags.TBODY,
    TFOOT: tags.TBODY,
    TH:    tags.TD
  });
})();

Element.Methods.Simulated = {
  hasAttribute: function(element, attribute) {
    attribute = Element._attributeTranslations.has[attribute] || attribute;
    var node = $(element).getAttributeNode(attribute);
    return !!(node && node.specified);
  }
};

Element.Methods.ByTag = { };

Object.extend(Element, Element.Methods);

(function(div) {

  if (!Prototype.BrowserFeatures.ElementExtensions && div['__proto__']) {
    window.HTMLElement = { };
    window.HTMLElement.prototype = div['__proto__'];
    Prototype.BrowserFeatures.ElementExtensions = true;
  }

  div = null;

})(document.createElement('div'))

Element.extend = (function() {

  function checkDeficiency(tagName) {
    if (typeof window.Element != 'undefined') {
      var proto = window.Element.prototype;
      if (proto) {
        var id = '_' + (Math.random()+'').slice(2);
        var el = document.createElement(tagName);
        proto[id] = 'x';
        var isBuggy = (el[id] !== 'x');
        delete proto[id];
        el = null;
        return isBuggy;
      }
    }
    return false;
  }

  function extendElementWith(element, methods) {
    for (var property in methods) {
      var value = methods[property];
      if (Object.isFunction(value) && !(property in element))
        element[property] = value.methodize();
    }
  }

  var HTMLOBJECTELEMENT_PROTOTYPE_BUGGY = checkDeficiency('object');

  if (Prototype.BrowserFeatures.SpecificElementExtensions) {
    if (HTMLOBJECTELEMENT_PROTOTYPE_BUGGY) {
      return function(element) {
        if (element && typeof element._extendedByPrototype == 'undefined') {
          var t = element.tagName;
          if (t && (/^(?:object|applet|embed)$/i.test(t))) {
            extendElementWith(element, Element.Methods);
            extendElementWith(element, Element.Methods.Simulated);
            extendElementWith(element, Element.Methods.ByTag[t.toUpperCase()]);
          }
        }
        return element;
      }
    }
    return Prototype.K;
  }

  var Methods = { }, ByTag = Element.Methods.ByTag;

  var extend = Object.extend(function(element) {
    if (!element || typeof element._extendedByPrototype != 'undefined' ||
        element.nodeType != 1 || element == window) return element;

    var methods = Object.clone(Methods),
        tagName = element.tagName.toUpperCase();

    if (ByTag[tagName]) Object.extend(methods, ByTag[tagName]);

    extendElementWith(element, methods);

    element._extendedByPrototype = Prototype.emptyFunction;
    return element;

  }, {
    refresh: function() {
      if (!Prototype.BrowserFeatures.ElementExtensions) {
        Object.extend(Methods, Element.Methods);
        Object.extend(Methods, Element.Methods.Simulated);
      }
    }
  });

  extend.refresh();
  return extend;
})();

Element.hasAttribute = function(element, attribute) {
  if (element.hasAttribute) return element.hasAttribute(attribute);
  return Element.Methods.Simulated.hasAttribute(element, attribute);
};

Element.addMethods = function(methods) {
  var F = Prototype.BrowserFeatures, T = Element.Methods.ByTag;

  if (!methods) {
    Object.extend(Form, Form.Methods);
    Object.extend(Form.Element, Form.Element.Methods);
    Object.extend(Element.Methods.ByTag, {
      "FORM":     Object.clone(Form.Methods),
      "INPUT":    Object.clone(Form.Element.Methods),
      "SELECT":   Object.clone(Form.Element.Methods),
      "TEXTAREA": Object.clone(Form.Element.Methods)
    });
  }

  if (arguments.length == 2) {
    var tagName = methods;
    methods = arguments[1];
  }

  if (!tagName) Object.extend(Element.Methods, methods || { });
  else {
    if (Object.isArray(tagName)) tagName.each(extend);
    else extend(tagName);
  }

  function extend(tagName) {
    tagName = tagName.toUpperCase();
    if (!Element.Methods.ByTag[tagName])
      Element.Methods.ByTag[tagName] = { };
    Object.extend(Element.Methods.ByTag[tagName], methods);
  }

  function copy(methods, destination, onlyIfAbsent) {
    onlyIfAbsent = onlyIfAbsent || false;
    for (var property in methods) {
      var value = methods[property];
      if (!Object.isFunction(value)) continue;
      if (!onlyIfAbsent || !(property in destination))
        destination[property] = value.methodize();
    }
  }

  function findDOMClass(tagName) {
    var klass;
    var trans = {
      "OPTGROUP": "OptGroup", "TEXTAREA": "TextArea", "P": "Paragraph",
      "FIELDSET": "FieldSet", "UL": "UList", "OL": "OList", "DL": "DList",
      "DIR": "Directory", "H1": "Heading", "H2": "Heading", "H3": "Heading",
      "H4": "Heading", "H5": "Heading", "H6": "Heading", "Q": "Quote",
      "INS": "Mod", "DEL": "Mod", "A": "Anchor", "IMG": "Image", "CAPTION":
      "TableCaption", "COL": "TableCol", "COLGROUP": "TableCol", "THEAD":
      "TableSection", "TFOOT": "TableSection", "TBODY": "TableSection", "TR":
      "TableRow", "TH": "TableCell", "TD": "TableCell", "FRAMESET":
      "FrameSet", "IFRAME": "IFrame"
    };
    if (trans[tagName]) klass = 'HTML' + trans[tagName] + 'Element';
    if (window[klass]) return window[klass];
    klass = 'HTML' + tagName + 'Element';
    if (window[klass]) return window[klass];
    klass = 'HTML' + tagName.capitalize() + 'Element';
    if (window[klass]) return window[klass];

    var element = document.createElement(tagName);
    var proto = element['__proto__'] || element.constructor.prototype;
    element = null;
    return proto;
  }

  var elementPrototype = window.HTMLElement ? HTMLElement.prototype :
   Element.prototype;

  if (F.ElementExtensions) {
    copy(Element.Methods, elementPrototype);
    copy(Element.Methods.Simulated, elementPrototype, true);
  }

  if (F.SpecificElementExtensions) {
    for (var tag in Element.Methods.ByTag) {
      var klass = findDOMClass(tag);
      if (Object.isUndefined(klass)) continue;
      copy(T[tag], klass.prototype);
    }
  }

  Object.extend(Element, Element.Methods);
  delete Element.ByTag;

  if (Element.extend.refresh) Element.extend.refresh();
  Element.cache = { };
};


document.viewport = {

  getDimensions: function() {
    return { width: this.getWidth(), height: this.getHeight() };
  },

  getScrollOffsets: function() {
    return Element._returnOffset(
      window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
      window.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop);
  }
};

(function(viewport) {
  var B = Prototype.Browser, doc = document, element, property = {};

  function getRootElement() {
    if (B.WebKit && !doc.evaluate)
      return document;

    if (B.Opera && window.parseFloat(window.opera.version()) < 9.5)
      return document.body;

    return document.documentElement;
  }

  function define(D) {
    if (!element) element = getRootElement();

    property[D] = 'client' + D;

    viewport['get' + D] = function() { return element[property[D]] };
    return viewport['get' + D]();
  }

  viewport.getWidth  = define.curry('Width');

  viewport.getHeight = define.curry('Height');
})(document.viewport);


Element.Storage = {
  UID: 1
};

Element.addMethods({
  getStorage: function(element) {
    if (!(element = $(element))) return;

    var uid;
    if (element === window) {
      uid = 0;
    } else {
      if (typeof element._prototypeUID === "undefined")
        element._prototypeUID = [Element.Storage.UID++];
      uid = element._prototypeUID[0];
    }

    if (!Element.Storage[uid])
      Element.Storage[uid] = $H();

    return Element.Storage[uid];
  },

  store: function(element, key, value) {
    if (!(element = $(element))) return;

    if (arguments.length === 2) {
      Element.getStorage(element).update(key);
    } else {
      Element.getStorage(element).set(key, value);
    }

    return element;
  },

  retrieve: function(element, key, defaultValue) {
    if (!(element = $(element))) return;
    var hash = Element.getStorage(element), value = hash.get(key);

    if (Object.isUndefined(value)) {
      hash.set(key, defaultValue);
      value = defaultValue;
    }

    return value;
  },

  clone: function(element, deep) {
    if (!(element = $(element))) return;
    var clone = element.cloneNode(deep);
    clone._prototypeUID = void 0;
    if (deep) {
      var descendants = Element.select(clone, '*'),
          i = descendants.length;
      while (i--) {
        descendants[i]._prototypeUID = void 0;
      }
    }
    return Element.extend(clone);
  }
});
/* Portions of the Selector class are derived from Jack Slocum's DomQuery,
 * part of YUI-Ext version 0.40, distributed under the terms of an MIT-style
 * license.  Please see http://www.yui-ext.com/ for more information. */

var Selector = Class.create({
  initialize: function(expression) {
    this.expression = expression.strip();

    if (this.shouldUseSelectorsAPI()) {
      this.mode = 'selectorsAPI';
    } else if (this.shouldUseXPath()) {
      this.mode = 'xpath';
      this.compileXPathMatcher();
    } else {
      this.mode = "normal";
      this.compileMatcher();
    }

  },

  shouldUseXPath: (function() {

    var IS_DESCENDANT_SELECTOR_BUGGY = (function(){
      var isBuggy = false;
      if (document.evaluate && window.XPathResult) {
        var el = document.createElement('div');
        el.innerHTML = '<ul><li></li></ul><div><ul><li></li></ul></div>';

        var xpath = ".//*[local-name()='ul' or local-name()='UL']" +
          "//*[local-name()='li' or local-name()='LI']";

        var result = document.evaluate(xpath, el, null,
          XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);

        isBuggy = (result.snapshotLength !== 2);
        el = null;
      }
      return isBuggy;
    })();

    return function() {
      if (!Prototype.BrowserFeatures.XPath) return false;

      var e = this.expression;

      if (Prototype.Browser.WebKit &&
       (e.include("-of-type") || e.include(":empty")))
        return false;

      if ((/(\[[\w-]*?:|:checked)/).test(e))
        return false;

      if (IS_DESCENDANT_SELECTOR_BUGGY) return false;

      return true;
    }

  })(),

  shouldUseSelectorsAPI: function() {
    if (!Prototype.BrowserFeatures.SelectorsAPI) return false;

    if (Selector.CASE_INSENSITIVE_CLASS_NAMES) return false;

    if (!Selector._div) Selector._div = new Element('div');

    try {
      Selector._div.querySelector(this.expression);
    } catch(e) {
      return false;
    }

    return true;
  },

  compileMatcher: function() {
    var e = this.expression, ps = Selector.patterns, h = Selector.handlers,
        c = Selector.criteria, le, p, m, len = ps.length, name;

    if (Selector._cache[e]) {
      this.matcher = Selector._cache[e];
      return;
    }

    this.matcher = ["this.matcher = function(root) {",
                    "var r = root, h = Selector.handlers, c = false, n;"];

    while (e && le != e && (/\S/).test(e)) {
      le = e;
      for (var i = 0; i<len; i++) {
        p = ps[i].re;
        name = ps[i].name;
        if (m = e.match(p)) {
          this.matcher.push(Object.isFunction(c[name]) ? c[name](m) :
            new Template(c[name]).evaluate(m));
          e = e.replace(m[0], '');
          break;
        }
      }
    }

    this.matcher.push("return h.unique(n);\n}");
    eval(this.matcher.join('\n'));
    Selector._cache[this.expression] = this.matcher;
  },

  compileXPathMatcher: function() {
    var e = this.expression, ps = Selector.patterns,
        x = Selector.xpath, le, m, len = ps.length, name;

    if (Selector._cache[e]) {
      this.xpath = Selector._cache[e]; return;
    }

    this.matcher = ['.//*'];
    while (e && le != e && (/\S/).test(e)) {
      le = e;
      for (var i = 0; i<len; i++) {
        name = ps[i].name;
        if (m = e.match(ps[i].re)) {
          this.matcher.push(Object.isFunction(x[name]) ? x[name](m) :
            new Template(x[name]).evaluate(m));
          e = e.replace(m[0], '');
          break;
        }
      }
    }

    this.xpath = this.matcher.join('');
    Selector._cache[this.expression] = this.xpath;
  },

  findElements: function(root) {
    root = root || document;
    var e = this.expression, results;

    switch (this.mode) {
      case 'selectorsAPI':
        if (root !== document) {
          var oldId = root.id, id = $(root).identify();
          id = id.replace(/([\.:])/g, "\\$1");
          e = "#" + id + " " + e;
        }

        results = $A(root.querySelectorAll(e)).map(Element.extend);
        root.id = oldId;

        return results;
      case 'xpath':
        return document._getElementsByXPath(this.xpath, root);
      default:
       return this.matcher(root);
    }
  },

  match: function(element) {
    this.tokens = [];

    var e = this.expression, ps = Selector.patterns, as = Selector.assertions;
    var le, p, m, len = ps.length, name;

    while (e && le !== e && (/\S/).test(e)) {
      le = e;
      for (var i = 0; i<len; i++) {
        p = ps[i].re;
        name = ps[i].name;
        if (m = e.match(p)) {
          if (as[name]) {
            this.tokens.push([name, Object.clone(m)]);
            e = e.replace(m[0], '');
          } else {
            return this.findElements(document).include(element);
          }
        }
      }
    }

    var match = true, name, matches;
    for (var i = 0, token; token = this.tokens[i]; i++) {
      name = token[0], matches = token[1];
      if (!Selector.assertions[name](element, matches)) {
        match = false; break;
      }
    }

    return match;
  },

  toString: function() {
    return this.expression;
  },

  inspect: function() {
    return "#<Selector:" + this.expression.inspect() + ">";
  }
});

if (Prototype.BrowserFeatures.SelectorsAPI &&
 document.compatMode === 'BackCompat') {
  Selector.CASE_INSENSITIVE_CLASS_NAMES = (function(){
    var div = document.createElement('div'),
     span = document.createElement('span');

    div.id = "prototype_test_id";
    span.className = 'Test';
    div.appendChild(span);
    var isIgnored = (div.querySelector('#prototype_test_id .test') !== null);
    div = span = null;
    return isIgnored;
  })();
}

Object.extend(Selector, {
  _cache: { },

  xpath: {
    descendant:   "//*",
    child:        "/*",
    adjacent:     "/following-sibling::*[1]",
    laterSibling: '/following-sibling::*',
    tagName:      function(m) {
      if (m[1] == '*') return '';
      return "[local-name()='" + m[1].toLowerCase() +
             "' or local-name()='" + m[1].toUpperCase() + "']";
    },
    className:    "[contains(concat(' ', @class, ' '), ' #{1} ')]",
    id:           "[@id='#{1}']",
    attrPresence: function(m) {
      m[1] = m[1].toLowerCase();
      return new Template("[@#{1}]").evaluate(m);
    },
    attr: function(m) {
      m[1] = m[1].toLowerCase();
      m[3] = m[5] || m[6];
      return new Template(Selector.xpath.operators[m[2]]).evaluate(m);
    },
    pseudo: function(m) {
      var h = Selector.xpath.pseudos[m[1]];
      if (!h) return '';
      if (Object.isFunction(h)) return h(m);
      return new Template(Selector.xpath.pseudos[m[1]]).evaluate(m);
    },
    operators: {
      '=':  "[@#{1}='#{3}']",
      '!=': "[@#{1}!='#{3}']",
      '^=': "[starts-with(@#{1}, '#{3}')]",
      '$=': "[substring(@#{1}, (string-length(@#{1}) - string-length('#{3}') + 1))='#{3}']",
      '*=': "[contains(@#{1}, '#{3}')]",
      '~=': "[contains(concat(' ', @#{1}, ' '), ' #{3} ')]",
      '|=': "[contains(concat('-', @#{1}, '-'), '-#{3}-')]"
    },
    pseudos: {
      'first-child': '[not(preceding-sibling::*)]',
      'last-child':  '[not(following-sibling::*)]',
      'only-child':  '[not(preceding-sibling::* or following-sibling::*)]',
      'empty':       "[count(*) = 0 and (count(text()) = 0)]",
      'checked':     "[@checked]",
      'disabled':    "[(@disabled) and (@type!='hidden')]",
      'enabled':     "[not(@disabled) and (@type!='hidden')]",
      'not': function(m) {
        var e = m[6], p = Selector.patterns,
            x = Selector.xpath, le, v, len = p.length, name;

        var exclusion = [];
        while (e && le != e && (/\S/).test(e)) {
          le = e;
          for (var i = 0; i<len; i++) {
            name = p[i].name
            if (m = e.match(p[i].re)) {
              v = Object.isFunction(x[name]) ? x[name](m) : new Template(x[name]).evaluate(m);
              exclusion.push("(" + v.substring(1, v.length - 1) + ")");
              e = e.replace(m[0], '');
              break;
            }
          }
        }
        return "[not(" + exclusion.join(" and ") + ")]";
      },
      'nth-child':      function(m) {
        return Selector.xpath.pseudos.nth("(count(./preceding-sibling::*) + 1) ", m);
      },
      'nth-last-child': function(m) {
        return Selector.xpath.pseudos.nth("(count(./following-sibling::*) + 1) ", m);
      },
      'nth-of-type':    function(m) {
        return Selector.xpath.pseudos.nth("position() ", m);
      },
      'nth-last-of-type': function(m) {
        return Selector.xpath.pseudos.nth("(last() + 1 - position()) ", m);
      },
      'first-of-type':  function(m) {
        m[6] = "1"; return Selector.xpath.pseudos['nth-of-type'](m);
      },
      'last-of-type':   function(m) {
        m[6] = "1"; return Selector.xpath.pseudos['nth-last-of-type'](m);
      },
      'only-of-type':   function(m) {
        var p = Selector.xpath.pseudos; return p['first-of-type'](m) + p['last-of-type'](m);
      },
      nth: function(fragment, m) {
        var mm, formula = m[6], predicate;
        if (formula == 'even') formula = '2n+0';
        if (formula == 'odd')  formula = '2n+1';
        if (mm = formula.match(/^(\d+)$/)) // digit only
          return '[' + fragment + "= " + mm[1] + ']';
        if (mm = formula.match(/^(-?\d*)?n(([+-])(\d+))?/)) { // an+b
          if (mm[1] == "-") mm[1] = -1;
          var a = mm[1] ? Number(mm[1]) : 1;
          var b = mm[2] ? Number(mm[2]) : 0;
          predicate = "[((#{fragment} - #{b}) mod #{a} = 0) and " +
          "((#{fragment} - #{b}) div #{a} >= 0)]";
          return new Template(predicate).evaluate({
            fragment: fragment, a: a, b: b });
        }
      }
    }
  },

  criteria: {
    tagName:      'n = h.tagName(n, r, "#{1}", c);      c = false;',
    className:    'n = h.className(n, r, "#{1}", c);    c = false;',
    id:           'n = h.id(n, r, "#{1}", c);           c = false;',
    attrPresence: 'n = h.attrPresence(n, r, "#{1}", c); c = false;',
    attr: function(m) {
      m[3] = (m[5] || m[6]);
      return new Template('n = h.attr(n, r, "#{1}", "#{3}", "#{2}", c); c = false;').evaluate(m);
    },
    pseudo: function(m) {
      if (m[6]) m[6] = m[6].replace(/"/g, '\\"');
      return new Template('n = h.pseudo(n, "#{1}", "#{6}", r, c); c = false;').evaluate(m);
    },
    descendant:   'c = "descendant";',
    child:        'c = "child";',
    adjacent:     'c = "adjacent";',
    laterSibling: 'c = "laterSibling";'
  },

  patterns: [
    { name: 'laterSibling', re: /^\s*~\s*/ },
    { name: 'child',        re: /^\s*>\s*/ },
    { name: 'adjacent',     re: /^\s*\+\s*/ },
    { name: 'descendant',   re: /^\s/ },

    { name: 'tagName',      re: /^\s*(\*|[\w\-]+)(\b|$)?/ },
    { name: 'id',           re: /^#([\w\-\*]+)(\b|$)/ },
    { name: 'className',    re: /^\.([\w\-\*]+)(\b|$)/ },
    { name: 'pseudo',       re: /^:((first|last|nth|nth-last|only)(-child|-of-type)|empty|checked|(en|dis)abled|not)(\((.*?)\))?(\b|$|(?=\s|[:+~>]))/ },
    { name: 'attrPresence', re: /^\[((?:[\w-]+:)?[\w-]+)\]/ },
    { name: 'attr',         re: /\[((?:[\w-]*:)?[\w-]+)\s*(?:([!^$*~|]?=)\s*((['"])([^\4]*?)\4|([^'"][^\]]*?)))?\]/ }
  ],

  assertions: {
    tagName: function(element, matches) {
      return matches[1].toUpperCase() == element.tagName.toUpperCase();
    },

    className: function(element, matches) {
      return Element.hasClassName(element, matches[1]);
    },

    id: function(element, matches) {
      return element.id === matches[1];
    },

    attrPresence: function(element, matches) {
      return Element.hasAttribute(element, matches[1]);
    },

    attr: function(element, matches) {
      var nodeValue = Element.readAttribute(element, matches[1]);
      return nodeValue && Selector.operators[matches[2]](nodeValue, matches[5] || matches[6]);
    }
  },

  handlers: {
    concat: function(a, b) {
      for (var i = 0, node; node = b[i]; i++)
        a.push(node);
      return a;
    },

    mark: function(nodes) {
      var _true = Prototype.emptyFunction;
      for (var i = 0, node; node = nodes[i]; i++)
        node._countedByPrototype = _true;
      return nodes;
    },

    unmark: (function(){

      var PROPERTIES_ATTRIBUTES_MAP = (function(){
        var el = document.createElement('div'),
            isBuggy = false,
            propName = '_countedByPrototype',
            value = 'x'
        el[propName] = value;
        isBuggy = (el.getAttribute(propName) === value);
        el = null;
        return isBuggy;
      })();

      return PROPERTIES_ATTRIBUTES_MAP ?
        function(nodes) {
          for (var i = 0, node; node = nodes[i]; i++)
            node.removeAttribute('_countedByPrototype');
          return nodes;
        } :
        function(nodes) {
          for (var i = 0, node; node = nodes[i]; i++)
            node._countedByPrototype = void 0;
          return nodes;
        }
    })(),

    index: function(parentNode, reverse, ofType) {
      parentNode._countedByPrototype = Prototype.emptyFunction;
      if (reverse) {
        for (var nodes = parentNode.childNodes, i = nodes.length - 1, j = 1; i >= 0; i--) {
          var node = nodes[i];
          if (node.nodeType == 1 && (!ofType || node._countedByPrototype)) node.nodeIndex = j++;
        }
      } else {
        for (var i = 0, j = 1, nodes = parentNode.childNodes; node = nodes[i]; i++)
          if (node.nodeType == 1 && (!ofType || node._countedByPrototype)) node.nodeIndex = j++;
      }
    },

    unique: function(nodes) {
      if (nodes.length == 0) return nodes;
      var results = [], n;
      for (var i = 0, l = nodes.length; i < l; i++)
        if (typeof (n = nodes[i])._countedByPrototype == 'undefined') {
          n._countedByPrototype = Prototype.emptyFunction;
          results.push(Element.extend(n));
        }
      return Selector.handlers.unmark(results);
    },

    descendant: function(nodes) {
      var h = Selector.handlers;
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        h.concat(results, node.getElementsByTagName('*'));
      return results;
    },

    child: function(nodes) {
      var h = Selector.handlers;
      for (var i = 0, results = [], node; node = nodes[i]; i++) {
        for (var j = 0, child; child = node.childNodes[j]; j++)
          if (child.nodeType == 1 && child.tagName != '!') results.push(child);
      }
      return results;
    },

    adjacent: function(nodes) {
      for (var i = 0, results = [], node; node = nodes[i]; i++) {
        var next = this.nextElementSibling(node);
        if (next) results.push(next);
      }
      return results;
    },

    laterSibling: function(nodes) {
      var h = Selector.handlers;
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        h.concat(results, Element.nextSiblings(node));
      return results;
    },

    nextElementSibling: function(node) {
      while (node = node.nextSibling)
        if (node.nodeType == 1) return node;
      return null;
    },

    previousElementSibling: function(node) {
      while (node = node.previousSibling)
        if (node.nodeType == 1) return node;
      return null;
    },

    tagName: function(nodes, root, tagName, combinator) {
      var uTagName = tagName.toUpperCase();
      var results = [], h = Selector.handlers;
      if (nodes) {
        if (combinator) {
          if (combinator == "descendant") {
            for (var i = 0, node; node = nodes[i]; i++)
              h.concat(results, node.getElementsByTagName(tagName));
            return results;
          } else nodes = this[combinator](nodes);
          if (tagName == "*") return nodes;
        }
        for (var i = 0, node; node = nodes[i]; i++)
          if (node.tagName.toUpperCase() === uTagName) results.push(node);
        return results;
      } else return root.getElementsByTagName(tagName);
    },

    id: function(nodes, root, id, combinator) {
      var targetNode = $(id), h = Selector.handlers;

      if (root == document) {
        if (!targetNode) return [];
        if (!nodes) return [targetNode];
      } else {
        if (!root.sourceIndex || root.sourceIndex < 1) {
          var nodes = root.getElementsByTagName('*');
          for (var j = 0, node; node = nodes[j]; j++) {
            if (node.id === id) return [node];
          }
        }
      }

      if (nodes) {
        if (combinator) {
          if (combinator == 'child') {
            for (var i = 0, node; node = nodes[i]; i++)
              if (targetNode.parentNode == node) return [targetNode];
          } else if (combinator == 'descendant') {
            for (var i = 0, node; node = nodes[i]; i++)
              if (Element.descendantOf(targetNode, node)) return [targetNode];
          } else if (combinator == 'adjacent') {
            for (var i = 0, node; node = nodes[i]; i++)
              if (Selector.handlers.previousElementSibling(targetNode) == node)
                return [targetNode];
          } else nodes = h[combinator](nodes);
        }
        for (var i = 0, node; node = nodes[i]; i++)
          if (node == targetNode) return [targetNode];
        return [];
      }
      return (targetNode && Element.descendantOf(targetNode, root)) ? [targetNode] : [];
    },

    className: function(nodes, root, className, combinator) {
      if (nodes && combinator) nodes = this[combinator](nodes);
      return Selector.handlers.byClassName(nodes, root, className);
    },

    byClassName: function(nodes, root, className) {
      if (!nodes) nodes = Selector.handlers.descendant([root]);
      var needle = ' ' + className + ' ';
      for (var i = 0, results = [], node, nodeClassName; node = nodes[i]; i++) {
        nodeClassName = node.className;
        if (nodeClassName.length == 0) continue;
        if (nodeClassName == className || (' ' + nodeClassName + ' ').include(needle))
          results.push(node);
      }
      return results;
    },

    attrPresence: function(nodes, root, attr, combinator) {
      if (!nodes) nodes = root.getElementsByTagName("*");
      if (nodes && combinator) nodes = this[combinator](nodes);
      var results = [];
      for (var i = 0, node; node = nodes[i]; i++)
        if (Element.hasAttribute(node, attr)) results.push(node);
      return results;
    },

    attr: function(nodes, root, attr, value, operator, combinator) {
      if (!nodes) nodes = root.getElementsByTagName("*");
      if (nodes && combinator) nodes = this[combinator](nodes);
      var handler = Selector.operators[operator], results = [];
      for (var i = 0, node; node = nodes[i]; i++) {
        var nodeValue = Element.readAttribute(node, attr);
        if (nodeValue === null) continue;
        if (handler(nodeValue, value)) results.push(node);
      }
      return results;
    },

    pseudo: function(nodes, name, value, root, combinator) {
      if (nodes && combinator) nodes = this[combinator](nodes);
      if (!nodes) nodes = root.getElementsByTagName("*");
      return Selector.pseudos[name](nodes, value, root);
    }
  },

  pseudos: {
    'first-child': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++) {
        if (Selector.handlers.previousElementSibling(node)) continue;
          results.push(node);
      }
      return results;
    },
    'last-child': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++) {
        if (Selector.handlers.nextElementSibling(node)) continue;
          results.push(node);
      }
      return results;
    },
    'only-child': function(nodes, value, root) {
      var h = Selector.handlers;
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        if (!h.previousElementSibling(node) && !h.nextElementSibling(node))
          results.push(node);
      return results;
    },
    'nth-child':        function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, formula, root);
    },
    'nth-last-child':   function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, formula, root, true);
    },
    'nth-of-type':      function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, formula, root, false, true);
    },
    'nth-last-of-type': function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, formula, root, true, true);
    },
    'first-of-type':    function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, "1", root, false, true);
    },
    'last-of-type':     function(nodes, formula, root) {
      return Selector.pseudos.nth(nodes, "1", root, true, true);
    },
    'only-of-type':     function(nodes, formula, root) {
      var p = Selector.pseudos;
      return p['last-of-type'](p['first-of-type'](nodes, formula, root), formula, root);
    },

    getIndices: function(a, b, total) {
      if (a == 0) return b > 0 ? [b] : [];
      return $R(1, total).inject([], function(memo, i) {
        if (0 == (i - b) % a && (i - b) / a >= 0) memo.push(i);
        return memo;
      });
    },

    nth: function(nodes, formula, root, reverse, ofType) {
      if (nodes.length == 0) return [];
      if (formula == 'even') formula = '2n+0';
      if (formula == 'odd')  formula = '2n+1';
      var h = Selector.handlers, results = [], indexed = [], m;
      h.mark(nodes);
      for (var i = 0, node; node = nodes[i]; i++) {
        if (!node.parentNode._countedByPrototype) {
          h.index(node.parentNode, reverse, ofType);
          indexed.push(node.parentNode);
        }
      }
      if (formula.match(/^\d+$/)) { // just a number
        formula = Number(formula);
        for (var i = 0, node; node = nodes[i]; i++)
          if (node.nodeIndex == formula) results.push(node);
      } else if (m = formula.match(/^(-?\d*)?n(([+-])(\d+))?/)) { // an+b
        if (m[1] == "-") m[1] = -1;
        var a = m[1] ? Number(m[1]) : 1;
        var b = m[2] ? Number(m[2]) : 0;
        var indices = Selector.pseudos.getIndices(a, b, nodes.length);
        for (var i = 0, node, l = indices.length; node = nodes[i]; i++) {
          for (var j = 0; j < l; j++)
            if (node.nodeIndex == indices[j]) results.push(node);
        }
      }
      h.unmark(nodes);
      h.unmark(indexed);
      return results;
    },

    'empty': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++) {
        if (node.tagName == '!' || node.firstChild) continue;
        results.push(node);
      }
      return results;
    },

    'not': function(nodes, selector, root) {
      var h = Selector.handlers, selectorType, m;
      var exclusions = new Selector(selector).findElements(root);
      h.mark(exclusions);
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        if (!node._countedByPrototype) results.push(node);
      h.unmark(exclusions);
      return results;
    },

    'enabled': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        if (!node.disabled && (!node.type || node.type !== 'hidden'))
          results.push(node);
      return results;
    },

    'disabled': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        if (node.disabled) results.push(node);
      return results;
    },

    'checked': function(nodes, value, root) {
      for (var i = 0, results = [], node; node = nodes[i]; i++)
        if (node.checked) results.push(node);
      return results;
    }
  },

  operators: {
    '=':  function(nv, v) { return nv == v; },
    '!=': function(nv, v) { return nv != v; },
    '^=': function(nv, v) { return nv == v || nv && nv.startsWith(v); },
    '$=': function(nv, v) { return nv == v || nv && nv.endsWith(v); },
    '*=': function(nv, v) { return nv == v || nv && nv.include(v); },
    '~=': function(nv, v) { return (' ' + nv + ' ').include(' ' + v + ' '); },
    '|=': function(nv, v) { return ('-' + (nv || "").toUpperCase() +
     '-').include('-' + (v || "").toUpperCase() + '-'); }
  },

  split: function(expression) {
    var expressions = [];
    expression.scan(/(([\w#:.~>+()\s-]+|\*|\[.*?\])+)\s*(,|$)/, function(m) {
      expressions.push(m[1].strip());
    });
    return expressions;
  },

  matchElements: function(elements, expression) {
    var matches = $$(expression), h = Selector.handlers;
    h.mark(matches);
    for (var i = 0, results = [], element; element = elements[i]; i++)
      if (element._countedByPrototype) results.push(element);
    h.unmark(matches);
    return results;
  },

  findElement: function(elements, expression, index) {
    if (Object.isNumber(expression)) {
      index = expression; expression = false;
    }
    return Selector.matchElements(elements, expression || '*')[index || 0];
  },

  findChildElements: function(element, expressions) {
    expressions = Selector.split(expressions.join(','));
    var results = [], h = Selector.handlers;
    for (var i = 0, l = expressions.length, selector; i < l; i++) {
      selector = new Selector(expressions[i].strip());
      h.concat(results, selector.findElements(element));
    }
    return (l > 1) ? h.unique(results) : results;
  }
});

if (Prototype.Browser.IE) {
  Object.extend(Selector.handlers, {
    concat: function(a, b) {
      for (var i = 0, node; node = b[i]; i++)
        if (node.tagName !== "!") a.push(node);
      return a;
    }
  });
}

function $$() {
  return Selector.findChildElements(document, $A(arguments));
}

var Form = {
  reset: function(form) {
    form = $(form);
    form.reset();
    return form;
  },

  serializeElements: function(elements, options) {
    if (typeof options != 'object') options = { hash: !!options };
    else if (Object.isUndefined(options.hash)) options.hash = true;
    var key, value, submitted = false, submit = options.submit;

    var data = elements.inject({ }, function(result, element) {
      if (!element.disabled && element.name) {
        key = element.name; value = $(element).getValue();
        if (value != null && element.type != 'file' && (element.type != 'submit' || (!submitted &&
            submit !== false && (!submit || key == submit) && (submitted = true)))) {
          if (key in result) {
            if (!Object.isArray(result[key])) result[key] = [result[key]];
            result[key].push(value);
          }
          else result[key] = value;
        }
      }
      return result;
    });

    return options.hash ? data : Object.toQueryString(data);
  }
};

Form.Methods = {
  serialize: function(form, options) {
    return Form.serializeElements(Form.getElements(form), options);
  },

  getElements: function(form) {
    var elements = $(form).getElementsByTagName('*'),
        element,
        arr = [ ],
        serializers = Form.Element.Serializers;
    for (var i = 0; element = elements[i]; i++) {
      arr.push(element);
    }
    return arr.inject([], function(elements, child) {
      if (serializers[child.tagName.toLowerCase()])
        elements.push(Element.extend(child));
      return elements;
    })
  },

  getInputs: function(form, typeName, name) {
    form = $(form);
    var inputs = form.getElementsByTagName('input');

    if (!typeName && !name) return $A(inputs).map(Element.extend);

    for (var i = 0, matchingInputs = [], length = inputs.length; i < length; i++) {
      var input = inputs[i];
      if ((typeName && input.type != typeName) || (name && input.name != name))
        continue;
      matchingInputs.push(Element.extend(input));
    }

    return matchingInputs;
  },

  disable: function(form) {
    form = $(form);
    Form.getElements(form).invoke('disable');
    return form;
  },

  enable: function(form) {
    form = $(form);
    Form.getElements(form).invoke('enable');
    return form;
  },

  findFirstElement: function(form) {
    var elements = $(form).getElements().findAll(function(element) {
      return 'hidden' != element.type && !element.disabled;
    });
    var firstByIndex = elements.findAll(function(element) {
      return element.hasAttribute('tabIndex') && element.tabIndex >= 0;
    }).sortBy(function(element) { return element.tabIndex }).first();

    return firstByIndex ? firstByIndex : elements.find(function(element) {
      return /^(?:input|select|textarea)$/i.test(element.tagName);
    });
  },

  focusFirstElement: function(form) {
    form = $(form);
    form.findFirstElement().activate();
    return form;
  },

  request: function(form, options) {
    form = $(form), options = Object.clone(options || { });

    var params = options.parameters, action = form.readAttribute('action') || '';
    if (action.blank()) action = window.location.href;
    options.parameters = form.serialize(true);

    if (params) {
      if (Object.isString(params)) params = params.toQueryParams();
      Object.extend(options.parameters, params);
    }

    if (form.hasAttribute('method') && !options.method)
      options.method = form.method;

    return new Ajax.Request(action, options);
  }
};

/*--------------------------------------------------------------------------*/


Form.Element = {
  focus: function(element) {
    $(element).focus();
    return element;
  },

  select: function(element) {
    $(element).select();
    return element;
  }
};

Form.Element.Methods = {

  serialize: function(element) {
    element = $(element);
    if (!element.disabled && element.name) {
      var value = element.getValue();
      if (value != undefined) {
        var pair = { };
        pair[element.name] = value;
        return Object.toQueryString(pair);
      }
    }
    return '';
  },

  getValue: function(element) {
    element = $(element);
    var method = element.tagName.toLowerCase();
    return Form.Element.Serializers[method](element);
  },

  setValue: function(element, value) {
    element = $(element);
    var method = element.tagName.toLowerCase();
    Form.Element.Serializers[method](element, value);
    return element;
  },

  clear: function(element) {
    $(element).value = '';
    return element;
  },

  present: function(element) {
    return $(element).value != '';
  },

  activate: function(element) {
    element = $(element);
    try {
      element.focus();
      if (element.select && (element.tagName.toLowerCase() != 'input' ||
          !(/^(?:button|reset|submit)$/i.test(element.type))))
        element.select();
    } catch (e) { }
    return element;
  },

  disable: function(element) {
    element = $(element);
    element.disabled = true;
    return element;
  },

  enable: function(element) {
    element = $(element);
    element.disabled = false;
    return element;
  }
};

/*--------------------------------------------------------------------------*/

var Field = Form.Element;

var $F = Form.Element.Methods.getValue;

/*--------------------------------------------------------------------------*/

Form.Element.Serializers = {
  input: function(element, value) {
    switch (element.type.toLowerCase()) {
      case 'checkbox':
      case 'radio':
        return Form.Element.Serializers.inputSelector(element, value);
      default:
        return Form.Element.Serializers.textarea(element, value);
    }
  },

  inputSelector: function(element, value) {
    if (Object.isUndefined(value)) return element.checked ? element.value : null;
    else element.checked = !!value;
  },

  textarea: function(element, value) {
    if (Object.isUndefined(value)) return element.value;
    else element.value = value;
  },

  select: function(element, value) {
    if (Object.isUndefined(value))
      return this[element.type == 'select-one' ?
        'selectOne' : 'selectMany'](element);
    else {
      var opt, currentValue, single = !Object.isArray(value);
      for (var i = 0, length = element.length; i < length; i++) {
        opt = element.options[i];
        currentValue = this.optionValue(opt);
        if (single) {
          if (currentValue == value) {
            opt.selected = true;
            return;
          }
        }
        else opt.selected = value.include(currentValue);
      }
    }
  },

  selectOne: function(element) {
    var index = element.selectedIndex;
    return index >= 0 ? this.optionValue(element.options[index]) : null;
  },

  selectMany: function(element) {
    var values, length = element.length;
    if (!length) return null;

    for (var i = 0, values = []; i < length; i++) {
      var opt = element.options[i];
      if (opt.selected) values.push(this.optionValue(opt));
    }
    return values;
  },

  optionValue: function(opt) {
    return Element.extend(opt).hasAttribute('value') ? opt.value : opt.text;
  }
};

/*--------------------------------------------------------------------------*/


Abstract.TimedObserver = Class.create(PeriodicalExecuter, {
  initialize: function($super, element, frequency, callback) {
    $super(callback, frequency);
    this.element   = $(element);
    this.lastValue = this.getValue();
  },

  execute: function() {
    var value = this.getValue();
    if (Object.isString(this.lastValue) && Object.isString(value) ?
        this.lastValue != value : String(this.lastValue) != String(value)) {
      this.callback(this.element, value);
      this.lastValue = value;
    }
  }
});

Form.Element.Observer = Class.create(Abstract.TimedObserver, {
  getValue: function() {
    return Form.Element.getValue(this.element);
  }
});

Form.Observer = Class.create(Abstract.TimedObserver, {
  getValue: function() {
    return Form.serialize(this.element);
  }
});

/*--------------------------------------------------------------------------*/

Abstract.EventObserver = Class.create({
  initialize: function(element, callback) {
    this.element  = $(element);
    this.callback = callback;

    this.lastValue = this.getValue();
    if (this.element.tagName.toLowerCase() == 'form')
      this.registerFormCallbacks();
    else
      this.registerCallback(this.element);
  },

  onElementEvent: function() {
    var value = this.getValue();
    if (this.lastValue != value) {
      this.callback(this.element, value);
      this.lastValue = value;
    }
  },

  registerFormCallbacks: function() {
    Form.getElements(this.element).each(this.registerCallback, this);
  },

  registerCallback: function(element) {
    if (element.type) {
      switch (element.type.toLowerCase()) {
        case 'checkbox':
        case 'radio':
          Event.observe(element, 'click', this.onElementEvent.bind(this));
          break;
        default:
          Event.observe(element, 'change', this.onElementEvent.bind(this));
          break;
      }
    }
  }
});

Form.Element.EventObserver = Class.create(Abstract.EventObserver, {
  getValue: function() {
    return Form.Element.getValue(this.element);
  }
});

Form.EventObserver = Class.create(Abstract.EventObserver, {
  getValue: function() {
    return Form.serialize(this.element);
  }
});
(function() {

  var Event = {
    KEY_BACKSPACE: 8,
    KEY_TAB:       9,
    KEY_RETURN:   13,
    KEY_ESC:      27,
    KEY_LEFT:     37,
    KEY_UP:       38,
    KEY_RIGHT:    39,
    KEY_DOWN:     40,
    KEY_DELETE:   46,
    KEY_HOME:     36,
    KEY_END:      35,
    KEY_PAGEUP:   33,
    KEY_PAGEDOWN: 34,
    KEY_INSERT:   45,

    cache: {}
  };

  var docEl = document.documentElement;
  var MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED = 'onmouseenter' in docEl
    && 'onmouseleave' in docEl;

  var _isButton;
  if (Prototype.Browser.IE) {
    var buttonMap = { 0: 1, 1: 4, 2: 2 };
    _isButton = function(event, code) {
      return event.button === buttonMap[code];
    };
  } else if (Prototype.Browser.WebKit) {
    _isButton = function(event, code) {
      switch (code) {
        case 0: return event.which == 1 && !event.metaKey;
        case 1: return event.which == 1 && event.metaKey;
        default: return false;
      }
    };
  } else {
    _isButton = function(event, code) {
      return event.which ? (event.which === code + 1) : (event.button === code);
    };
  }

  function isLeftClick(event)   { return _isButton(event, 0) }

  function isMiddleClick(event) { return _isButton(event, 1) }

  function isRightClick(event)  { return _isButton(event, 2) }

  function element(event) {
    event = Event.extend(event);

    var node = event.target, type = event.type,
     currentTarget = event.currentTarget;

    if (currentTarget && currentTarget.tagName) {
      if (type === 'load' || type === 'error' ||
        (type === 'click' && currentTarget.tagName.toLowerCase() === 'input'
          && currentTarget.type === 'radio'))
            node = currentTarget;
    }

    if (node.nodeType == Node.TEXT_NODE)
      node = node.parentNode;

    return Element.extend(node);
  }

  function findElement(event, expression) {
    var element = Event.element(event);
    if (!expression) return element;
    var elements = [element].concat(element.ancestors());
    return Selector.findElement(elements, expression, 0);
  }

  function pointer(event) {
    return { x: pointerX(event), y: pointerY(event) };
  }

  function pointerX(event) {
    var docElement = document.documentElement,
     body = document.body || { scrollLeft: 0 };

    return event.pageX || (event.clientX +
      (docElement.scrollLeft || body.scrollLeft) -
      (docElement.clientLeft || 0));
  }

  function pointerY(event) {
    var docElement = document.documentElement,
     body = document.body || { scrollTop: 0 };

    return  event.pageY || (event.clientY +
       (docElement.scrollTop || body.scrollTop) -
       (docElement.clientTop || 0));
  }


  function stop(event) {
    Event.extend(event);
    event.preventDefault();
    event.stopPropagation();

    event.stopped = true;
  }

  Event.Methods = {
    isLeftClick: isLeftClick,
    isMiddleClick: isMiddleClick,
    isRightClick: isRightClick,

    element: element,
    findElement: findElement,

    pointer: pointer,
    pointerX: pointerX,
    pointerY: pointerY,

    stop: stop
  };


  var methods = Object.keys(Event.Methods).inject({ }, function(m, name) {
    m[name] = Event.Methods[name].methodize();
    return m;
  });

  if (Prototype.Browser.IE) {
    function _relatedTarget(event) {
      var element;
      switch (event.type) {
        case 'mouseover': element = event.fromElement; break;
        case 'mouseout':  element = event.toElement;   break;
        default: return null;
      }
      return Element.extend(element);
    }

    Object.extend(methods, {
      stopPropagation: function() { this.cancelBubble = true },
      preventDefault:  function() { this.returnValue = false },
      inspect: function() { return '[object Event]' }
    });

    Event.extend = function(event, element) {
      if (!event) return false;
      if (event._extendedByPrototype) return event;

      event._extendedByPrototype = Prototype.emptyFunction;
      var pointer = Event.pointer(event);

      Object.extend(event, {
        target: event.srcElement || element,
        relatedTarget: _relatedTarget(event),
        pageX:  pointer.x,
        pageY:  pointer.y
      });

      return Object.extend(event, methods);
    };
  } else {
    Event.prototype = window.Event.prototype || document.createEvent('HTMLEvents').__proto__;
    Object.extend(Event.prototype, methods);
    Event.extend = Prototype.K;
  }

  function _createResponder(element, eventName, handler) {
    var registry = Element.retrieve(element, 'prototype_event_registry');

    if (Object.isUndefined(registry)) {
      CACHE.push(element);
      registry = Element.retrieve(element, 'prototype_event_registry', $H());
    }

    var respondersForEvent = registry.get(eventName);
    if (Object.isUndefined(respondersForEvent)) {
      respondersForEvent = [];
      registry.set(eventName, respondersForEvent);
    }

    if (respondersForEvent.pluck('handler').include(handler)) return false;

    var responder;
    if (eventName.include(":")) {
      responder = function(event) {
        if (Object.isUndefined(event.eventName))
          return false;

        if (event.eventName !== eventName)
          return false;

        Event.extend(event, element);
        handler.call(element, event);
      };
    } else {
      if (!MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED &&
       (eventName === "mouseenter" || eventName === "mouseleave")) {
        if (eventName === "mouseenter" || eventName === "mouseleave") {
          responder = function(event) {
            Event.extend(event, element);

            var parent = event.relatedTarget;
            while (parent && parent !== element) {
              try { parent = parent.parentNode; }
              catch(e) { parent = element; }
            }

            if (parent === element) return;

            handler.call(element, event);
          };
        }
      } else {
        responder = function(event) {
          Event.extend(event, element);
          handler.call(element, event);
        };
      }
    }

    responder.handler = handler;
    respondersForEvent.push(responder);
    return responder;
  }

  function _destroyCache() {
    for (var i = 0, length = CACHE.length; i < length; i++) {
      Event.stopObserving(CACHE[i]);
      CACHE[i] = null;
    }
  }

  var CACHE = [];

  if (Prototype.Browser.IE)
    window.attachEvent('onunload', _destroyCache);

  if (Prototype.Browser.WebKit)
    window.addEventListener('unload', Prototype.emptyFunction, false);


  var _getDOMEventName = Prototype.K;

  if (!MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED) {
    _getDOMEventName = function(eventName) {
      var translations = { mouseenter: "mouseover", mouseleave: "mouseout" };
      return eventName in translations ? translations[eventName] : eventName;
    };
  }

  function observe(element, eventName, handler) {
    element = $(element);

    var responder = _createResponder(element, eventName, handler);

    if (!responder) return element;

    if (eventName.include(':')) {
      if (element.addEventListener)
        element.addEventListener("dataavailable", responder, false);
      else {
        element.attachEvent("ondataavailable", responder);
        element.attachEvent("onfilterchange", responder);
      }
    } else {
      var actualEventName = _getDOMEventName(eventName);

      if (element.addEventListener)
        element.addEventListener(actualEventName, responder, false);
      else
        element.attachEvent("on" + actualEventName, responder);
    }

    return element;
  }

  function stopObserving(element, eventName, handler) {
    element = $(element);

    var registry = Element.retrieve(element, 'prototype_event_registry');

    if (Object.isUndefined(registry)) return element;

    if (eventName && !handler) {
      var responders = registry.get(eventName);

      if (Object.isUndefined(responders)) return element;

      responders.each( function(r) {
        Element.stopObserving(element, eventName, r.handler);
      });
      return element;
    } else if (!eventName) {
      registry.each( function(pair) {
        var eventName = pair.key, responders = pair.value;

        responders.each( function(r) {
          Element.stopObserving(element, eventName, r.handler);
        });
      });
      return element;
    }

    var responders = registry.get(eventName);

    if (!responders) return;

    var responder = responders.find( function(r) { return r.handler === handler; });
    if (!responder) return element;

    var actualEventName = _getDOMEventName(eventName);

    if (eventName.include(':')) {
      if (element.removeEventListener)
        element.removeEventListener("dataavailable", responder, false);
      else {
        element.detachEvent("ondataavailable", responder);
        element.detachEvent("onfilterchange",  responder);
      }
    } else {
      if (element.removeEventListener)
        element.removeEventListener(actualEventName, responder, false);
      else
        element.detachEvent('on' + actualEventName, responder);
    }

    registry.set(eventName, responders.without(responder));

    return element;
  }

  function fire(element, eventName, memo, bubble) {
    element = $(element);

    if (Object.isUndefined(bubble))
      bubble = true;

    if (element == document && document.createEvent && !element.dispatchEvent)
      element = document.documentElement;

    var event;
    if (document.createEvent) {
      event = document.createEvent('HTMLEvents');
      event.initEvent('dataavailable', true, true);
    } else {
      event = document.createEventObject();
      event.eventType = bubble ? 'ondataavailable' : 'onfilterchange';
    }

    event.eventName = eventName;
    event.memo = memo || { };

    if (document.createEvent)
      element.dispatchEvent(event);
    else
      element.fireEvent(event.eventType, event);

    return Event.extend(event);
  }


  Object.extend(Event, Event.Methods);

  Object.extend(Event, {
    fire:          fire,
    observe:       observe,
    stopObserving: stopObserving
  });

  Element.addMethods({
    fire:          fire,

    observe:       observe,

    stopObserving: stopObserving
  });

  Object.extend(document, {
    fire:          fire.methodize(),

    observe:       observe.methodize(),

    stopObserving: stopObserving.methodize(),

    loaded:        false
  });

  if (window.Event) Object.extend(window.Event, Event);
  else window.Event = Event;
})();

(function() {
  /* Support for the DOMContentLoaded event is based on work by Dan Webb,
     Matthias Miller, Dean Edwards, John Resig, and Diego Perini. */

  var timer;

  function fireContentLoadedEvent() {
    if (document.loaded) return;
    if (timer) window.clearTimeout(timer);
    document.loaded = true;
    document.fire('dom:loaded');
  }

  function checkReadyState() {
    if (document.readyState === 'complete') {
      document.stopObserving('readystatechange', checkReadyState);
      fireContentLoadedEvent();
    }
  }

  function pollDoScroll() {
    try { document.documentElement.doScroll('left'); }
    catch(e) {
      timer = pollDoScroll.defer();
      return;
    }
    fireContentLoadedEvent();
  }

  if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fireContentLoadedEvent, false);
  } else {
    document.observe('readystatechange', checkReadyState);
    if (window == top)
      timer = pollDoScroll.defer();
  }

  Event.observe(window, 'load', fireContentLoadedEvent);
})();

Element.addMethods();

/*------------------------------- DEPRECATED -------------------------------*/

Hash.toQueryString = Object.toQueryString;

var Toggle = { display: Element.toggle };

Element.Methods.childOf = Element.Methods.descendantOf;

var Insertion = {
  Before: function(element, content) {
    return Element.insert(element, {before:content});
  },

  Top: function(element, content) {
    return Element.insert(element, {top:content});
  },

  Bottom: function(element, content) {
    return Element.insert(element, {bottom:content});
  },

  After: function(element, content) {
    return Element.insert(element, {after:content});
  }
};

var $continue = new Error('"throw $continue" is deprecated, use "return" instead');

var Position = {
  includeScrollOffsets: false,

  prepare: function() {
    this.deltaX =  window.pageXOffset
                || document.documentElement.scrollLeft
                || document.body.scrollLeft
                || 0;
    this.deltaY =  window.pageYOffset
                || document.documentElement.scrollTop
                || document.body.scrollTop
                || 0;
  },

  within: function(element, x, y) {
    if (this.includeScrollOffsets)
      return this.withinIncludingScrolloffsets(element, x, y);
    this.xcomp = x;
    this.ycomp = y;
    this.offset = Element.cumulativeOffset(element);

    return (y >= this.offset[1] &&
            y <  this.offset[1] + element.offsetHeight &&
            x >= this.offset[0] &&
            x <  this.offset[0] + element.offsetWidth);
  },

  withinIncludingScrolloffsets: function(element, x, y) {
    var offsetcache = Element.cumulativeScrollOffset(element);

    this.xcomp = x + offsetcache[0] - this.deltaX;
    this.ycomp = y + offsetcache[1] - this.deltaY;
    this.offset = Element.cumulativeOffset(element);

    return (this.ycomp >= this.offset[1] &&
            this.ycomp <  this.offset[1] + element.offsetHeight &&
            this.xcomp >= this.offset[0] &&
            this.xcomp <  this.offset[0] + element.offsetWidth);
  },

  overlap: function(mode, element) {
    if (!mode) return 0;
    if (mode == 'vertical')
      return ((this.offset[1] + element.offsetHeight) - this.ycomp) /
        element.offsetHeight;
    if (mode == 'horizontal')
      return ((this.offset[0] + element.offsetWidth) - this.xcomp) /
        element.offsetWidth;
  },


  cumulativeOffset: Element.Methods.cumulativeOffset,

  positionedOffset: Element.Methods.positionedOffset,

  absolutize: function(element) {
    Position.prepare();
    return Element.absolutize(element);
  },

  relativize: function(element) {
    Position.prepare();
    return Element.relativize(element);
  },

  realOffset: Element.Methods.cumulativeScrollOffset,

  offsetParent: Element.Methods.getOffsetParent,

  page: Element.Methods.viewportOffset,

  clone: function(source, target, options) {
    options = options || { };
    return Element.clonePosition(target, source, options);
  }
};

/*--------------------------------------------------------------------------*/

if (!document.getElementsByClassName) document.getElementsByClassName = function(instanceMethods){
  function iter(name) {
    return name.blank() ? null : "[contains(concat(' ', @class, ' '), ' " + name + " ')]";
  }

  instanceMethods.getElementsByClassName = Prototype.BrowserFeatures.XPath ?
  function(element, className) {
    className = className.toString().strip();
    var cond = /\s/.test(className) ? $w(className).map(iter).join('') : iter(className);
    return cond ? document._getElementsByXPath('.//*' + cond, element) : [];
  } : function(element, className) {
    className = className.toString().strip();
    var elements = [], classNames = (/\s/.test(className) ? $w(className) : null);
    if (!classNames && !className) return elements;

    var nodes = $(element).getElementsByTagName('*');
    className = ' ' + className + ' ';

    for (var i = 0, child, cn; child = nodes[i]; i++) {
      if (child.className && (cn = ' ' + child.className + ' ') && (cn.include(className) ||
          (classNames && classNames.all(function(name) {
            return !name.toString().blank() && cn.include(' ' + name + ' ');
          }))))
        elements.push(Element.extend(child));
    }
    return elements;
  };

  return function(className, parentElement) {
    return $(parentElement || document.body).getElementsByClassName(className);
  };
}(Element.Methods);

/*--------------------------------------------------------------------------*/

Element.ClassNames = Class.create();
Element.ClassNames.prototype = {
  initialize: function(element) {
    this.element = $(element);
  },

  _each: function(iterator) {
    this.element.className.split(/\s+/).select(function(name) {
      return name.length > 0;
    })._each(iterator);
  },

  set: function(className) {
    this.element.className = className;
  },

  add: function(classNameToAdd) {
    if (this.include(classNameToAdd)) return;
    this.set($A(this).concat(classNameToAdd).join(' '));
  },

  remove: function(classNameToRemove) {
    if (!this.include(classNameToRemove)) return;
    this.set($A(this).without(classNameToRemove).join(' '));
  },

  toString: function() {
    return $A(this).join(' ');
  }
};

Object.extend(Element.ClassNames.prototype, Enumerable);

/*--------------------------------------------------------------------------*/

//------------------------------------------------------------------------------
// INFO: il faut étendre Element avant toute utilisation de $
//------------------------------------------------------------------------------
// 1. Ajout de méthodes aux éléments de type formulaire
//------------------------------------------------------------------------------
var WebrsaFormTags = ['BUTTON', 'INPUT', 'OPTGROUP', 'OPTION', 'SELECT', 'TEXTAREA'];

var WebrsaFormMethods = {
	enabled: function (element) {
		var disabled;
		element = $(element);
		disabled = element.readAttribute('disabled');
		return (
			undefined === element.readAttribute('disabled')
			|| null === element.readAttribute('disabled')
		);
	}
};

Element.addMethods(WebrsaFormTags, WebrsaFormMethods);

//------------------------------------------------------------------------------
// 2. Surcharge des méthodes enable et disable
//------------------------------------------------------------------------------

Form.Element.Methods.disable = Form.Element.Methods.disable.wrap(
	function (callOriginal, element) {
		if(-1 === ['option', 'optgroup'].indexOf(element.tagName.toLowerCase())) {
			var wrapper = $(element).up([ 'div.input', 'div.checkbox' ]);
			if(wrapper) {
				wrapper.addClassName( 'disabled' );
			}
		}
		return callOriginal(element);
	}
);

Form.Element.Methods.enable = Form.Element.Methods.enable.wrap(
	function (callOriginal, element) {
		if(-1 === ['option', 'optgroup'].indexOf(element.tagName.toLowerCase())) {
			var wrapper = $(element).up([ 'div.input', 'div.checkbox' ]);
			if(wrapper) {
				wrapper.removeClassName( 'disabled' );
			}
		}
		return callOriginal(element);
	}
);

Object.extend(Form.Element, Form.Element.Methods);
Element.addMethods(WebrsaFormTags, Form.Element.Methods);
/**
 * @author Ryan Johnson <http://syntacticx.com/>
 * @copyright 2008 PersonalGrid Corporation <http://personalgrid.com/>
 * @package LivePipe UI
 * @license MIT
 * @url http://livepipe.net/core
 * @require prototype.js
 */

if(typeof(Control) == 'undefined')
    Control = {};

var $proc = function(proc){
    return typeof(proc) == 'function' ? proc : function(){return proc};
};

var $value = function(value){
    return typeof(value) == 'function' ? value() : value;
};

Object.Event = {
    extend: function(object){
        object._objectEventSetup = function(event_name){
            this._observers = this._observers || {};
            this._observers[event_name] = this._observers[event_name] || [];
        };
        object.observe = function(event_name,observer){
            if(typeof(event_name) == 'string' && typeof(observer) != 'undefined'){
                this._objectEventSetup(event_name);
                if(!this._observers[event_name].include(observer))
                    this._observers[event_name].push(observer);
            }else
                for(var e in event_name)
                    this.observe(e,event_name[e]);
        };
        object.stopObserving = function(event_name,observer){
            this._objectEventSetup(event_name);
            if(event_name && observer)
                this._observers[event_name] = this._observers[event_name].without(observer);
            else if(event_name)
                this._observers[event_name] = [];
            else
                this._observers = {};
        };
        object.observeOnce = function(event_name,outer_observer){
            var inner_observer = function(){
                outer_observer.apply(this,arguments);
                this.stopObserving(event_name,inner_observer);
            }.bind(this);
            this._objectEventSetup(event_name);
            this._observers[event_name].push(inner_observer);
        };
        object.notify = function(event_name){
            this._objectEventSetup(event_name);
            var collected_return_values = [];
            var args = $A(arguments).slice(1);
            try{
                for(var i = 0; i < this._observers[event_name].length; ++i)
                    collected_return_values.push(this._observers[event_name][i].apply(this._observers[event_name][i],args) || null);
            }catch(e){
                if(e == $break)
                    return false;
                else
                    throw e;
            }
            return collected_return_values;
        };
        if(object.prototype){
            object.prototype._objectEventSetup = object._objectEventSetup;
            object.prototype.observe = object.observe;
            object.prototype.stopObserving = object.stopObserving;
            object.prototype.observeOnce = object.observeOnce;
            object.prototype.notify = function(event_name){
                if(object.notify){
                    var args = $A(arguments).slice(1);
                    args.unshift(this);
                    args.unshift(event_name);
                    object.notify.apply(object,args);
                }
                this._objectEventSetup(event_name);
                var args = $A(arguments).slice(1);
                var collected_return_values = [];
                try{
                    if(this.options && this.options[event_name] && typeof(this.options[event_name]) == 'function')
                        collected_return_values.push(this.options[event_name].apply(this,args) || null);
                    for(var i = 0; i < this._observers[event_name].length; ++i)
                        collected_return_values.push(this._observers[event_name][i].apply(this._observers[event_name][i],args) || null);
                }catch(e){
                    if(e == $break)
                        return false;
                    else
                        throw e;
                }
                return collected_return_values;
            };
        }
    }
};

/* Begin Core Extensions */

//Element.observeOnce
Element.addMethods({
    observeOnce: function(element,event_name,outer_callback){
        var inner_callback = function(){
            outer_callback.apply(this,arguments);
            Element.stopObserving(element,event_name,inner_callback);
        };
        Element.observe(element,event_name,inner_callback);
    }
});

//mouse:wheel
(function(){
    function wheel(event){
        var delta, element, custom_event;
        // normalize the delta
        if (event.wheelDelta) { // IE & Opera
            delta = event.wheelDelta / 120;
        } else if (event.detail) { // W3C
            delta =- event.detail / 3;
        }
        if (!delta) { return; }
        element = Event.extend(event).target;
        element = Element.extend(element.nodeType === Node.TEXT_NODE ? element.parentNode : element);
        custom_event = element.fire('mouse:wheel',{ delta: delta });
        if (custom_event.stopped) {
            Event.stop(event);
            return false;
        }
    }
    document.observe('mousewheel',wheel);
    document.observe('DOMMouseScroll',wheel);
})();

/* End Core Extensions */

//from PrototypeUI
var IframeShim = Class.create({
    initialize: function() {
        this.element = new Element('iframe',{
            style: 'position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);display:none',
            src: 'javascript:void(0);',
            frameborder: 0
        });
        $(document.body).insert(this.element);
    },
    hide: function() {
        this.element.hide();
        return this;
    },
    show: function() {
        this.element.show();
        return this;
    },
    positionUnder: function(element) {
        var element = $(element);
        var offset = element.cumulativeOffset();
        var dimensions = element.getDimensions();
        this.element.setStyle({
            left: offset[0] + 'px',
            top: offset[1] + 'px',
            width: dimensions.width + 'px',
            height: dimensions.height + 'px',
            zIndex: element.getStyle('zIndex') - 1
        }).show();
        return this;
    },
    setBounds: function(bounds) {
        for(prop in bounds)
            bounds[prop] += 'px';
        this.element.setStyle(bounds);
        return this;
    },
    destroy: function() {
        if(this.element)
            this.element.remove();
        return this;
    }
});

/**
 * @author Ryan Johnson <http://syntacticx.com/>
 * @copyright 2008 PersonalGrid Corporation <http://personalgrid.com/>
 * @package LivePipe UI
 * @license MIT
 * @url http://livepipe.net/control/tabs
 * @require prototype.js, livepipe.js
 */

/*global window, document, Prototype, $, $A, $H, $break, Class, Element, Event, Control */

if(typeof(Prototype) == "undefined") {
    throw "Control.Tabs requires Prototype to be loaded."; }
if(typeof(Object.Event) == "undefined") {
    throw "Control.Tabs requires Object.Event to be loaded."; }

Control.Tabs = Class.create({
    initialize: function(tab_list_container,options){
        if(!$(tab_list_container)) {
            throw "Control.Tabs could not find the element: " + tab_list_container; }
        this.activeContainer = false;
        this.activeLink = false;
        this.containers = $H({});
        this.links = [];
        Control.Tabs.instances.push(this);
        this.options = {
            beforeChange: Prototype.emptyFunction,
            afterChange: Prototype.emptyFunction,
            hover: false,
            linkSelector: 'li a',
            setClassOnContainer: false,
            activeClassName: 'active',
            defaultTab: 'first',
            autoLinkExternal: true,
            targetRegExp: /#(.+)$/,
            showFunction: Element.show,
            hideFunction: Element.hide
        };
        Object.extend(this.options,options || {});
        (typeof(this.options.linkSelector == 'string') ?
            $(tab_list_container).select(this.options.linkSelector) :
            this.options.linkSelector($(tab_list_container))
        ).findAll(function(link){
            return (/^#/).exec((Prototype.Browser.WebKit ? decodeURIComponent(link.href) : link.href).replace(window.location.href.split('#')[0],''));
        }).each(function(link){
            this.addTab(link);
        }.bind(this));
        this.containers.values().each(Element.hide);
        if(this.options.defaultTab == 'first') {
            this.setActiveTab(this.links.first());
        } else if(this.options.defaultTab == 'last') {
            this.setActiveTab(this.links.last());
        } else {
            this.setActiveTab(this.options.defaultTab); }
        var targets = this.options.targetRegExp.exec(window.location);
        if(targets && targets[1]){
            targets[1].split(',').each(function(target){
                this.setActiveTab(this.links.find(function(link){
                    return link.key == target;
                }));
            }.bind(this));
        }
        if(this.options.autoLinkExternal){
            $A(document.getElementsByTagName('a')).each(function(a){
                if(!this.links.include(a)){
                    var clean_href = a.href.replace(window.location.href.split('#')[0],'');
                    if(clean_href.substring(0,1) == '#'){
                        if(this.containers.keys().include(clean_href.substring(1))){
                            $(a).observe('click',function(event,clean_href){
                                this.setActiveTab(clean_href.substring(1));
                            }.bindAsEventListener(this,clean_href));
                        }
                    }
                }
            }.bind(this));
        }
    },
    addTab: function(link){
        this.links.push(link);
        link.key = link.getAttribute('href').replace(window.location.href.split('#')[0],'').split('#').last().replace(/#/,'');
        var container = $(link.key);
        if(!container) {
            throw "Control.Tabs: #" + link.key + " was not found on the page."; }
        this.containers.set(link.key,container);
        link[this.options.hover ? 'onmouseover' : 'onclick'] = function(link){
            if(window.event) {
                Event.stop(window.event); }
            this.setActiveTab(link);
            return false;
        }.bind(this,link);
    },
    setActiveTab: function(link){
        if(!link && typeof(link) == 'undefined') {
            return; }
        if(typeof(link) == 'string'){
            this.setActiveTab(this.links.find(function(_link){
                return _link.key == link;
            }));
        }else if(typeof(link) == 'number'){
            this.setActiveTab(this.links[link]);
        }else{
            if(this.notify('beforeChange',this.activeContainer,this.containers.get(link.key)) === false) {
                return; }
            if(this.activeContainer) {
                this.options.hideFunction(this.activeContainer); }
            this.links.each(function(item){
                (this.options.setClassOnContainer ? $(item.parentNode) : item).removeClassName(this.options.activeClassName);
            }.bind(this));
            (this.options.setClassOnContainer ? $(link.parentNode) : link).addClassName(this.options.activeClassName);
            this.activeContainer = this.containers.get(link.key);
            this.activeLink = link;
            this.options.showFunction(this.containers.get(link.key));
            this.notify('afterChange',this.containers.get(link.key));
        }
    },
    next: function(){
        this.links.each(function(link,i){
            if(this.activeLink == link && this.links[i + 1]){
                this.setActiveTab(this.links[i + 1]);
                throw $break;
            }
        }.bind(this));
    },
    previous: function(){
        this.links.each(function(link,i){
            if(this.activeLink == link && this.links[i - 1]){
                this.setActiveTab(this.links[i - 1]);
                throw $break;
            }
        }.bind(this));
    },
    first: function(){
        this.setActiveTab(this.links.first());
    },
    last: function(){
        this.setActiveTab(this.links.last());
    }
});
Object.extend(Control.Tabs,{
    instances: [],
    findByTabId: function(id){
        return Control.Tabs.instances.find(function(tab){
            return tab.links.find(function(link){
                return link.key == id;
            });
        });
    }
});
Object.Event.extend(Control.Tabs);

/*
 * Copyright (c) 2006 Jonathan Weiss <jw@innerewut.de>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */


/* tooltip-0.2.js - Small tooltip library on top of Prototype 
 * by Jonathan Weiss <jw@innerewut.de> distributed under the BSD license. 
 *
 * This tooltip library works in two modes. If it gets a valid DOM element 
 * or DOM id as an argument it uses this element as the tooltip. This 
 * element will be placed (and shown) near the mouse pointer when a trigger-
 * element is moused-over.
 * If it gets only a text as an argument instead of a DOM id or DOM element
 * it will create a div with the classname 'tooltip' that holds the given text.
 * This newly created div will be used as the tooltip. This is usefull if you 
 * want to use tooltip.js to create popups out of title attributes.
 * 
 *
 * Usage: 
 *   <script src="/javascripts/prototype.js" type="text/javascript"></script>
 *   <script src="/javascripts/tooltip.js" type="text/javascript"></script>
 *   <script type="text/javascript">
 *     // with valid DOM id
 *     var my_tooltip = new Tooltip('id_of_trigger_element', 'id_of_tooltip_to_show_element')
 *
 *     // with text
 *     var my_other_tooltip = new Tooltip('id_of_trigger_element', 'a nice description')
 *
 *     // create popups for each element with a title attribute
 *    Event.observe(window,"load",function() {
 *      $$("*").findAll(function(node){
 *        return node.getAttribute('title');
 *      }).each(function(node){
 *        new Tooltip(node,node.title);
 *        node.removeAttribute("title");
 *      });
 *    });
 *    
 *   </script>
 * 
 * Now whenever you trigger a mouseOver on the `trigger` element, the tooltip element will
 * be shown. On o mouseOut the tooltip disappears. 
 * 
 * Example:
 * 
 *   <script src="/javascripts/prototype.js" type="text/javascript"></script>
 *   <script src="/javascripts/scriptaculous.js" type="text/javascript"></script>
 *   <script src="/javascripts/tooltip.js" type="text/javascript"></script>
 *
 *   <div id='tooltip' style="display:none; margin: 5px; background-color: red;">
 *     Detail infos on product 1....<br />
 *   </div>
 *
 *   <div id='product_1'>
 *     This is product 1
 *   </div>
 *
 *   <script type="text/javascript">
 *     var my_tooltip = new Tooltip('product_1', 'tooltip')
 *   </script>
 *
 * You can use my_tooltip.destroy() to remove the event observers and thereby the tooltip.
 */

var Tooltip = Class.create();
Tooltip.prototype = {
  initialize: function(element, tool_tip) {
    var options = Object.extend({
      default_css: false,
      margin: "0px",
	    padding: "5px",
	    backgroundColor: "#d6d6fc",
	    min_distance_x: 5,
      min_distance_y: 5,
      delta_x: 0,
      delta_y: 0,
      zindex: 1000
    }, arguments[2] || {});

    this.element      = $(element);

    this.options      = options;
    
    // use the supplied tooltip element or create our own div
    if($(tool_tip)) {
      this.tool_tip = $(tool_tip);
    } else {
      this.tool_tip = $(document.createElement("div")); 
      document.body.appendChild(this.tool_tip);
      this.tool_tip.addClassName("tooltip");
      this.tool_tip.appendChild(document.createTextNode(tool_tip));
    }

    // hide the tool-tip by default
    this.tool_tip.hide();

    this.eventMouseOver = this.showTooltip.bindAsEventListener(this);
    this.eventMouseOut   = this.hideTooltip.bindAsEventListener(this);
    this.eventMouseMove  = this.moveTooltip.bindAsEventListener(this);

    this.registerEvents();
  },

  destroy: function() {
    Event.stopObserving(this.element, "mouseover", this.eventMouseOver);
    Event.stopObserving(this.element, "mouseout", this.eventMouseOut);
    Event.stopObserving(this.element, "mousemove", this.eventMouseMove);
  },

  registerEvents: function() {
    Event.observe(this.element, "mouseover", this.eventMouseOver);
    Event.observe(this.element, "mouseout", this.eventMouseOut);
    Event.observe(this.element, "mousemove", this.eventMouseMove);
  },

  moveTooltip: function(event){
	  Event.stop(event);
	  // get Mouse position
    var mouse_x = Event.pointerX(event);
	  var mouse_y = Event.pointerY(event);

	  // decide if wee need to switch sides for the tooltip
	  var dimensions = Element.getDimensions( this.tool_tip );
	  var element_width = dimensions.width;
	  var element_height = dimensions.height;
	
	  if ( (element_width + mouse_x) >= ( this.getWindowWidth() - this.options.min_distance_x) ){ // too big for X
		  mouse_x = mouse_x - element_width;
		  // apply min_distance to make sure that the mouse is not on the tool-tip
		  mouse_x = mouse_x - this.options.min_distance_x;
	  } else {
		  mouse_x = mouse_x + this.options.min_distance_x;
	  }
	
	  mouse_y = mouse_y - element_height;
	  // apply min_distance to make sure that the mouse is not on the tool-tip
	  mouse_y = mouse_y - this.options.min_distance_y;

	  // now set the right styles
	  this.setStyles(mouse_x, mouse_y);
  },
	
		
  showTooltip: function(event) {
    Event.stop(event);
    this.moveTooltip(event);
	  new Element.show(this.tool_tip);
    this.tool_tip.style.display ='block';
  },
  
  setStyles: function(x, y){
    // set the right styles to position the tool tip
	  Element.setStyle(this.tool_tip, { position:'absolute',
	 								    top:y + this.options.delta_y + "px",
	 								    left:x + this.options.delta_x + "px",
									    zindex:this.options.zindex
	 								  });
	
	  // apply default theme if wanted
	  if (this.options.default_css){
	  	  Element.setStyle(this.tool_tip, { margin:this.options.margin,
		 		  						                    padding:this.options.padding,
		                                      backgroundColor:this.options.backgroundColor,
										                      zindex:this.options.zindex
		 								    });	
	  }	
  },

  hideTooltip: function(event){
	  new Element.hide(this.tool_tip);
  },

  getWindowHeight: function(){
    var innerHeight;
	  if (navigator.appVersion.indexOf('MSIE')>0) {
		  innerHeight = document.body.clientHeight;
    } else {
		  innerHeight = window.innerHeight;
    }
    return innerHeight;	
  },
 
  getWindowWidth: function(){
    var innerWidth;
	  if (navigator.appVersion.indexOf('MSIE')>0) {
		  innerWidth = document.body.clientWidth;
    } else {
		  innerWidth = window.innerWidth;
    }
    return innerWidth;	
  }

}

/*
 * ...
 */
var Webrsa = ( function() {
	'use strict';

	var date = {
			/**
			 * Convertit une date générée par le FormHelper de CakePHP (sous forme de
			 * trois listes déroulantes) en un objet Date javascript.
			 *
			 * @param {String} prefix Le préfixe de l'id des listes déroulantes (ex. UserBirthday)
			 * @returns {Date|null}
			 */
			'fromCakeSelects': function( prefix ) {
				/*global $F, console */
				var result = null;

				try {
					result = new Date( 1970, 0, 1, 0, 0, 0, 0 );
					result.setDate( parseInt( $F( prefix + 'Day' ), 10 ) );
					result.setMonth( parseInt( $F( prefix + 'Month' ), 10 ) - 1 );
					result.setYear( parseInt( $F( prefix + 'Year' ), 10 ) );
				} catch( e ) {
					console.log( e );
				}

				return result;
			},
			/**
			 * Convertit une date et une éventuelle heure en un objet Date
			 * javascript.
			 *
			 * Les formats acceptés sont:
			 *	- Dates seules: JJ/MM/AAAA, J/M/AAAA, J/M/AA, ...
			 *	- Partie heures: HH:MM:SS, H:M:S, HH:MM, H:M
			 *	- Dates et heures: <Date> à <Heure>, <Date> <Heure>
			 *
			 * @param {String} text La chaîne de caractères contenant la date (et l'heure)
			 * @returns {Date|null}
			 */
			'fromText': function( text ) {
				/*global console, regexps */
				var result = null, matches;

				try {
					matches = text.match( regexps.datetime() );

					if( null !== matches ) {
						result = new Date( 1970, 0, 1, 0, 0, 0, 0 );
						result.setDate( parseInt( matches[1], 10 ) );
						result.setMonth( parseInt( matches[2], 10 ) - 1 );
						result.setYear( parseInt( matches[3], 10 ) );

						if( undefined !== matches[4] ) {
							result.setHours( parseInt( matches[6], 10 ) );
							result.setMinutes( parseInt( matches[7], 10 ) );
							if( undefined !== matches[8] ) {
								result.setSeconds( parseInt( matches[9], 10 ) );
							}
						}
					}
				} catch( e ) {
					console.log( e );
				}

				return result;
			}
		},
		regexps = {
			'datetime': function() {
				return ( /^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})( (à ){0,1}([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2})){0,1}){0,1}$/ );
			}
		};

	return {
		'Date': date,
		'Regexps': regexps
	};
} () );

//-----------------------------------------------------------------------------

function make_folded_forms() {
	$$( 'form.folded' ).each( function( elmt ) {
//         var a = new Element( 'a', { 'class': 'toggler', 'href': '#', 'onclick' : '$( '' + $( elmt ).id + '' ).toggle(); return false;' } ).update( 'Visibilité formulaire' );
//         var p = a.wrap( 'p' );
//         $( elmt ).insert( { 'before' : p } );
		$( elmt ).hide();
	} );
}

//-----------------------------------------------------------------------------

function make_treemenus( absoluteBaseUrl, large, urlmenu ) {
	var dir = absoluteBaseUrl + 'img/icons';
	$$( '.treemenu li' ).each( function ( elmtLi ) {
		if( elmtLi.down( 'ul' ) ) {
			if( large ) {
				var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px'
				} );
			}
			else  {
				var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );
			}
			var link = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );
			var sign = '+';

			$( link ).observe( 'click', function( event ) {
				var innerUl = $( this ).up( 'li' ).down( 'ul' );
				innerUl.toggle();
				if( innerUl.visible() ) {
					$( this ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
					$( this ).down( 'img' ).alt = 'Réduire';
				}
				else {
					$( this ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
					$( this ).down( 'img' ).alt = 'Étendre';
				}
				return false;
			} );

			$( elmtLi ).down( 1 ).insert( { 'before' : link } );
			$( elmtLi ).down( 'ul' ).hide();
		}
	} );

	var currentUrl = location.href.replace( absoluteBaseUrl, '/' ).replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' ).replace( /#$/, '' );;
//	var relBaseUrl = absoluteBaseUrl.replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' );

	var menuUl = $$( '.treemenu > ul' )[0];

	$$( '.treemenu a' ).each( function ( elmtA ) {
		// TODO: plus propre
		var elmtAUrl = elmtA.href.replace( absoluteBaseUrl, '/' ).replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' );

		if(
			elmtAUrl == currentUrl
			|| elmtAUrl == currentUrl.replace( '/edit/', '/view/' )
			|| elmtAUrl == currentUrl.replace( '/add/', '/view/' )
			|| elmtAUrl == currentUrl.replace( '/add/', '/index/' )
			|| ( ( urlmenu !== null ) && ( elmtAUrl === urlmenu ) ) ) {
			$( elmtA ).addClassName( 'selected' );

			var ancestorsDone = false;
			elmtA.ancestors().each( function ( aAncestor ) {
				if( aAncestor == menuUl ) {
					ancestorsDone = true;
				}
				else if( !ancestorsDone ) {
					$( aAncestor ).addClassName( 'selected' );
					aAncestor.show();
					if( aAncestor.tagName == 'LI' ) {
						var toggler = aAncestor.down( 'a.toggler img' );
						if( toggler != undefined ) {
							toggler.src = dir + '/bullet_toggle_minus2.png';
							toggler.alt = 'Réduire';
						}
					}
				}
			} );

			// Montrer son descendant direct
			try {
				var upLi = elmtA.up( 'li' );
				if( upLi != undefined ) {
					var ul = upLi.down( 'ul' );
					if( ul != undefined ) {
						ul.show();
					}
				}
			}
			catch( e ) {
			}
		}
	} );
}

/// Fonction permettant "d'enrouler" le menu du dossier allocataire
function expandableTreeMenuContent( elmt, sign, dir ) {
	$( elmt ).up( 'ul' ).getElementsBySelector( 'li > a.toggler' ).each( function( elmtA ) {
		if( sign == 'plus' ) {
			elmtA.up( 'li' ).down( 'ul' ).show();
		}
		else {
			elmtA.up( 'li' ).down( 'ul' ).hide();
		}

		if( elmtA.down( 'img' ) != undefined ) {
			if( sign == 'plus' ) {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				elmtA.down( 'img' ).alt = 'Réduire';
			}
			else {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				elmtA.down( 'img' ).alt = 'Étendre';
			}
		}
	} );
}

/// Fonction permettant "de dérouler" le menu du dossier allocataire
function treeMenuExpandsAll( absoluteBaseUrl ) {

	var toggleLink = $( 'treemenuToggleLink' );
	var dir = absoluteBaseUrl + 'img/icons';

	var sign = $( toggleLink ).down( 'img' ).src.replace( new RegExp( '^.*(minus|plus).*' ), '$1' );

	$$( '.treemenu > ul > li > a.toggler' ).each( function ( elmtA ) {
		// Montrer tous les ancètres
		if( sign == 'plus' ) {
			elmtA.up( 'li' ).down( 'ul' ).show();
		}
		else {
			elmtA.up( 'li' ).down( 'ul' ).hide();
		}

		if( elmtA.down( 'img' ) != undefined ) {
			if( sign == 'plus' ) {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				elmtA.down( 'img' ).alt = 'Réduire';
			}
			else {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				elmtA.down( 'img' ).alt = 'Étendre';
			}
		}

		expandableTreeMenuContent( elmtA, sign, dir );
	} );

	if( sign == 'plus' ) {
		$( toggleLink ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
	}
	else {

		$( toggleLink ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
	}
}

//-----------------------------------------------------------------------------





// TODO: mettre avant les actions
// function make_table_tooltips() {
//     $$( 'table.tooltips' ).each( function ( elmtTable ) {
//         // FIXME: colspans dans le thead -> alert( $( this ).attr( 'colspan' ) );
//         var tooltipPositions = new Array();
//         var tooltipHeaders = new Array();
//         var actionPositions = new Array();
//
//         var iPosition = 0;
//         elmtTable.getElementsBySelector( 'thead tr th' ).each( function ( elmtTh ) {
//             var colspan = ( $( elmtTh ).readAttribute( 'colspan' ) != undefined ) ? $( elmtTh ).readAttribute( 'colspan' ) : 1;
//             if( elmtTh.hasClassName( 'tooltip' ) ) {
//                 elmtTh.remove();
//                 for( k = 0 ; k < colspan ; k++ )
//                     tooltipPositions.push( iPosition + k );
//                 tooltipHeaders[iPosition] = elmtTh.innerHTML;
//             }
//             if( elmtTh.hasClassName( 'action' ) ) {
//                 for( k = 0 ; k < colspan ; k++ )
//                     actionPositions.push( iPosition + k );
//             }
//             iPosition++;
//         } );
//
//         // FIXME
//         var th = new Element( 'th', { 'class': 'tooltip_table' } ).update( 'Informations complémentaires' );
//         $( elmtTable ).down( 'thead' ).down( 'tr' ).insert( { 'bottom' : th } );
//
//         elmtTable.getElementsBySelector( 'tbody tr' ).each( function ( elmtTbodyTr ) {
//             var tooltip_table = new Element( 'table', { 'class': 'tooltip' } );
//
//             var iPosition = 0;
//             elmtTbodyTr.getElementsBySelector( 'td' ).each( function ( elmtTbodyTd ) {
//                 if( tooltipPositions.include( iPosition ) ) {
//                     var tooltip_tr = new Element( 'tr', {} );
//                     var tooltip_th = new Element( 'th', {} ).update( tooltipHeaders[iPosition] );
//                     var tooltip_td = new Element( 'td', {} ).update( elmtTbodyTd.innerHTML );
//                     tooltip_tr.insert( { 'bottom' : tooltip_th } );
//                     tooltip_tr.insert( { 'bottom' : tooltip_td } );
//                     $( tooltip_table ).insert( { 'bottom' : tooltip_tr } );
//                     elmtTbodyTd.remove();
//                 }
//                 else if( actionPositions.include( iPosition ) ) {
//                     $( elmtTbodyTd ).addClassName( 'action' );
//                 }
//                 iPosition++;
//             } );
//
//             var tooltip_td = new Element( 'td', { 'class': 'tooltip_table' } );
//             $( tooltip_td ).insert( { 'bottom' : tooltip_table } );
//             $( elmtTbodyTr ).insert( { 'bottom' : tooltip_td } );
//         } );
//
//         elmtTable.getElementsBySelector( 'tbody tr td' ).each( function ( elmtTd ) {
//             // Mouse over
//             $( elmtTd ).observe( 'mouseover', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).addClassName( 'hover' ); // INFO: IE6
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'left' : ( event.pointerX() + 5 ) + 'px',
//                             'top' : ( event.pointerY() + 5 ) + 'px',
//                             'display' : 'block'
//                         } );
//                     } );
//                 }
//             } );
//
//             // Mouse move
//             $( elmtTd ).observe( 'mousemove', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'left' : ( event.pointerX() + 5 ) + 'px',
//                             'top' : ( event.pointerY() + 5 ) + 'px'
//                         } );
//                     } );
//                 }
//             } );
//
//             // Mouse out
//             $( elmtTd ).observe( 'mouseout', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).removeClassName( 'hover' ); // INFO: IE6
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'display' : 'none'
//                         } );
//                     } );
//                 }
//             } );
//         } );
//     } );
// }

//*****************************************************************************

function mkTooltipTables() {
	var tips = new Array();
	$$( 'table.tooltips' ).each( function( table ) {
		var actionPositions = new Array(),
			inputPositions = new Array(),
			realPosition = 0,
			headRows = $( table ).getElementsBySelector( 'thead tr' ),
			loop = 0;

		$( headRows ).each( function( headRow ) {
			loop++;
			$( headRow ).getElementsBySelector( 'th' ).each( function ( th ) {
				if( loop === headRows.length ) {
					var colspan = ( $( th ).readAttribute( 'colspan' ) != undefined ) ? $( th ).readAttribute( 'colspan' ) : 1;

					if( $( th ).hasClassName( 'action' ) ) {
						for( var k = 0 ; k < colspan ; k++ ) {
							actionPositions.push( realPosition + k );
						}
					}
					else if( $( th ).hasClassName( 'input' ) ) {
						for( var k = 0 ; k < colspan ; k++ ) {
							inputPositions.push( realPosition + k );
						}
					}
					realPosition = ( parseInt( realPosition ) + parseInt( colspan ) );
				}

				if( $( th ).hasClassName( 'innerTableHeader' ) ) {
					$( th ).addClassName( 'dynamic' );
				}
			} );
		} );

		var iPosition = 0;
		$( table ).getElementsBySelector( 'tbody tr' ).each( function( tr ) {
			if( $( tr ).up( '.innerTableCell' ) == undefined ) {
				$( tr ).addClassName( 'dynamic' );
				var jPosition = 0;
				$( tr ).childElements().each( function( td ) {
					if(
						( !actionPositions.include( jPosition ) && $(td).hasClassName('action') === false )
						&& ( !inputPositions.include( jPosition ) && $(td).hasClassName('input') === false )
					) {
						tips.push( new Tooltip( $( td ), 'innerTable' + $( table ).readAttribute( 'id' ) + iPosition ) );
					}
					jPosition++;
				} );
				iPosition++;
			}
		} );
	} );
}

//*****************************************************************************

function disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var cb = $( cbId );
	var checked = ( ( $F( cb ) == null ) ? false : true );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( field != null ) {
			if( checked != condition ) {
				field.enable();
				//ajout
				if( toggleVisibility ) {
					field.show();
				}
				//fin ajout
				var div = field.up( 'div.input' );
				if( !div ) {
					field.up( 'div.checkbox' );
				}

				if( div ) {
					div.removeClassName( 'disabled' );
					if( toggleVisibility ) {
						div.show();
					}
				}
			}
			else {
				field.disable();
				//ajout
				if( toggleVisibility ) {
					field.hide();
				}
				//fin ajout
				var div = field.up( 'div.input' );
				if( !div ) {
					field.up( 'div.checkbox' );
				}

				if( div ) {
					div.addClassName( 'disabled' );
					if( toggleVisibility ) {
						div.hide();
					}
				}
			}
		}
	} );
}

//-----------------------------------------------------------------------------

function observeDisableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility ) {
        toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility );

	var cb = $( cbId );
	$( cb ).observe( 'click', function( event ) { // FIXME change ?
		disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	var select = $( selectId );

	var result = false;
	value.each( function( elmt ) {
		if( $F( select ) == elmt ) {
			result = true;
		}
	} );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( field != null ) {
			if( result == condition ) {

				field.disable();

				if( input = field.up( 'div.input' ) )
					input.addClassName( 'disabled' );
				else if( input = field.up( 'div.checkbox' ) )
					input.addClassName( 'disabled' );

				if( toggleVisibility ) {
					input.hide();
				}
			}
			else {
   				field.enable();

				if( input = field.up( 'div.input' ) )
					input.removeClassName( 'disabled' );
				else if( input = field.up( 'div.checkbox' ) )
					input.removeClassName( 'disabled' );

				if( toggleVisibility ) {
					input.show();
				}
			}
		}
	} );
}
//----------------------------------------------------------------------------

function observeDisableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility );

	$( selectId ).observe( 'change', function( event ) {
		disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	var select = $( selectId );

	var result = false;
	value.each( function( elmt ) {
		if( $F( select ) == elmt ) {
			result = true;
		}
	} );

	var fieldset = $( fieldsetId );

	if( result ) {
		fieldset.removeClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.show();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.removeClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.enable() ne fonctionne pas avec des button
			try{
				elmt.enable();
			} catch( err ) {
				elmt.disabled = false;
			}

		} );
	}
	else {
		fieldset.addClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.hide();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.addClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.disable() ne fonctionne pas avec des button
			try{
				elmt.disable();
			} catch( err ) {
				elmt.disabled = true;
			}
		} );
	}

}

//----------------------------------------------------------------------------

function observeDisableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility );

	var select = $( selectId );
	$( select ).observe( 'change', function( event ) {
		disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var cb = $( cbId );
	var checked = ( ( $F( cb ) == null ) ? false : true );
	var fieldset = $( fieldsetId );

	if( checked != condition ) {
		fieldset.removeClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.show();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.removeClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.disable() ne fonctionne pas avec des button
			try{
				elmt.enable();
			} catch( err ) {
				elmt.disabled = false;
			}
		} );
	}
	else {
		fieldset.addClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.hide();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.addClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.enable() ne fonctionne pas avec des button
			try{
				elmt.disable();
			} catch( err ) {
				elmt.disabled = true;
			}
		} );
	}
}

//-----------------------------------------------------------------------------

function observeDisableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility );

	var cb = $( cbId );
	$( cb ).observe( 'click', function( event ) { // FIXME change ?
		disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility );
	} );
}

//*****************************************************************************
// @deprecated
function disableFieldsOnBoolean( field, fieldsIds, value, condition ) {
	var disabled = !( ( $F( field ) == value ) == condition );
	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( !disabled ) {
			field.enable();
			if( input = field.up( 'div.input' ) )
				input.removeClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.removeClassName( 'disabled' );
		}
		else {
			field.disable();
			if( input = field.up( 'div.input' ) )
				input.addClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.addClassName( 'disabled' );
		}
	} );
}

//-----------------------------------------------------------------------------
// @deprecated
function observeDisableFieldsOnBoolean( prefix, fieldsIds, value, condition ) {
	if( value == '1' ) {
		var otherValue = '0';
		disableFieldsOnBoolean( prefix + otherValue, fieldsIds, otherValue, !condition );
	}
	else {
		var otherValue = '1';
		disableFieldsOnBoolean( prefix + value, fieldsIds, value, condition );
	}

	$( prefix + value ).observe( 'click', function( event ) {
		disableFieldsOnBoolean( prefix + value, fieldsIds, value, condition );
	} );

	$( prefix + otherValue ).observe( 'click', function( event ) {
		disableFieldsOnBoolean( prefix + otherValue, fieldsIds, otherValue, !condition );
	} );
}

//-----------------------------------------------------------------------------

function setDateInterval( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) - 1 );
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au derenier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}

	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();
}

//-----------------------------------------------------------------------------

function setDateInterval2( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) ); //FIXME: suppression du -1 afin d'obtenir le nombre de mois exact
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au dernier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}


	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value =  $( masterPrefix + 'Day' ).value;//( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();

	// Calcul du dernier jour du mois
	var slaveDate = new Date();
	slaveDate.setDate( 1 );
	slaveDate.setMonth( $( slavePrefix + 'Month' ).value ); // FIXME ?
	slaveDate.setYear( $( slavePrefix + 'Year' ).value );
	slaveDate.setDate( slaveDate.getDate() - 1 );
	if( slaveDate.getDate() < $( slavePrefix + 'Day' ).value ) {
		$( slavePrefix + 'Day' ).value = slaveDate.getDate();
	}
}

function setDateIntervalCer( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// -------------------------------------------------------------------------
	// Initialisation
	var d = new Date();
	d.setYear( parseInt( $F( masterPrefix + 'Year' ), 10 ) );
	d.setMonth( parseInt( $F( masterPrefix + 'Month' ), 10 ) - 1 );
	d.setDate( parseInt( $F( masterPrefix + 'Day' ), 10 ) );
	// -------------------------------------------------------------------------
	// Calcul de la nouvelle date: nombre de mois en plus, 1 jour en moins
	d.setMonth( d.getMonth() + parseInt( nMonths, 10 ) );
	d.setDate( d.getDate() - 1 );
	// -------------------------------------------------------------------------
	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Year' ).value = d.getFullYear();

	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;

	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
}


function setNbDayInterval( masterPrefix, slavePrefix, nDays ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) - 1 );
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au derenier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}

	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();
}

//==============================================================================

function disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility ) {
	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	var disabled = false;
	value.each( function( elmt ) {
		if( !( ( currentValue == elmt ) == condition ) ) {
			disabled = true;
		}
	} );

	//var disabled = !( ( currentValue == value ) == condition );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( !disabled ) {


			field.enable();

			if( input = field.up( 'div.input' ) )
				input.removeClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.removeClassName( 'disabled' );

			//Ajout suite aux modifs ds les traitements PDOs
			if( toggleVisibility ) {
				input.show();
			}
		}
		else {

			field.disable();


			if( input = field.up( 'div.input' ) )
				input.addClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.addClassName( 'disabled' );

			//Ajout suite aux modifs ds les traitements PDOs
			if( toggleVisibility ) {
				input.hide();
			}
		}
	} );
}

//-----------------------------------------------------------------------------

function observeDisableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility );

	var v = $( form ).getInputs( 'radio', radioName );
	var currentValue = undefined;
	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility );
		} );
	} );
}


//*****************************************************************************

function disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility ) {
	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var v = $( form ).getInputs( 'radio', radioName );

	var fieldset = $( fieldsetId );

	if( fieldset != null ) {
		var currentValue = undefined;

		$( v ).each( function( radio ) {
			if( radio.checked ) {
				currentValue = radio.value;
			}
		} );

		var disabled = false;
		value.each( function( elmt ) {
			if( !( ( currentValue == elmt ) == condition ) ) {
				disabled = true;
			}
		} );

		if( disabled != condition ) {
			fieldset.removeClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.show();
			}

			$$( '#'+fieldset.id+' div.input, #'+fieldset.id+' radio' ).each( function( elmt ) {
				elmt.removeClassName( 'disabled' );
			} );

			$$( '#'+fieldset.id+' input, #'+fieldset.id+' select, #'+fieldset.id+' button, #'+fieldset.id+' textarea' ).each( function( elmt ) {
				// INFO: elmt.enable() ne fonctionne pas avec des button
				try{
					elmt.enable();
				} catch( err ) {
					elmt.disabled = false;
				}
			} );
		}
		else {
			fieldset.addClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.hide();
			}

			$$( '#'+fieldset.id+' div.input, #'+fieldset.id+' radio' ).each( function( elmt ) {
				elmt.addClassName( 'disabled' );
			} );

			$$( '#'+fieldset.id+' input, #'+fieldset.id+' select, #'+fieldset.id+' button, #'+fieldset.id+' textarea' ).each( function( elmt ) {
				// INFO: elmt.disable() ne fonctionne pas avec des button
				try{
					elmt.disable();
				} catch( err ) {
					elmt.disabled = true;
				}
			} );
		}
	}
}

//-----------------------------------------------------------------------------

function observeDisableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility );

	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;

	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility );
		} );
	} );
}

//-----------------------------------------------------------------------------

function makeTabbed( wrapperId, titleLevel ) {
	var ul = new Element( 'ul', { 'class' : 'ui-tabs-nav' } );
	$$( '#' + wrapperId + ' h' + titleLevel + '.title' ).each( function( title ) {
		var parent = title.up();
		var classNames = $( title ).readAttribute( 'class' ).replace( /title/, 'tab' );
		var link = new Element( 'a', { href: '#' + parent.id } ).update( title.innerHTML );

		var titleAttr = $( title ).readAttribute( 'title' );
		if( titleAttr !== null && titleAttr !== '' ) {
			$( link ).writeAttribute( 'title', titleAttr );
		}

		var li = new Element( 'li', { 'class' : classNames } ).update( link );
		ul.appendChild( li );
		parent.addClassName( 'tab' );
		title.addClassName( 'tab hidden' );
	} );

	$( wrapperId ).insert( { 'before' : ul } );

	new Control.Tabs( ul );
}

//-----------------------------------------------------------------------------

function make_treemenus_droits( absoluteBaseUrl, large ) {
	var dir = absoluteBaseUrl + 'img/icons';

	$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
		if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {

			var thisTr = $( elmtTd ).up( 'tr' );
			var nextTr = $( thisTr ).next( 'tr' );
			var value = 2;
			var etat = 'fermer';
			while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
				var checkboxes = $( nextTr ).getElementsBySelector( 'input[type=checkbox]' );
				if ( value == 2) { value = $F( checkboxes[0] ); }
				else if ( value != $F( checkboxes[0] )) { etat = 'ouvert'; }
				nextTr = $( nextTr ).next( 'tr' );
			}

			if( etat == 'fermer' ) {
				if( large )
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px' } );
				else
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );

				nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.hide();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
			else {
				if( large )
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_minus2.png', 'alt': 'Réduire', 'width': '12px' } );
				else
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_minus2.png', 'alt': 'Réduire' } );
			}

			// INFO: onclick -> return false est indispensable.
			var link = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );

			$( link ).observe( 'click', function( event ) {
				var nextTr = $( this ).up( 'td' ).up( 'tr' ).next( 'tr' );

				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.toggle();

					if( nextTr.visible() ) {
						$( this ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
						$( this ).down( 'img' ).alt = 'Réduire';
					}
					else {
						$( this ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
						$( this ).down( 'img' ).alt = 'Étendre';
					}

					nextTr = $( nextTr ).next( 'tr' );
				}
			} );

			$( elmtTd ).insert( { 'top' : link } );
		}
	} );

	var tabledroit = $$( '#tableEditDroits' ).each(function (elmt) {
		if( large )
			var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px' } );
		else
			var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );

		var biglink = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );

		$( biglink ).observe( 'click', function( event ) {
			$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
				if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {
					var nextTr = $( elmtTd ).up( 'tr' ).next( 'tr' );

					while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
						if( $( elmt ).down( 'img' ).alt == 'Étendre' ) {
							$( elmtTd ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
							$( elmtTd ).down( 'img' ).alt = 'Réduire';
							nextTr.show();
						}
						else {
							$( elmtTd ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
							$( elmtTd ).down( 'img' ).alt = 'Étendre';
							nextTr.hide();
						}

						nextTr = $( nextTr ).next( 'tr' );
					}
				}
			} );
			if( $( elmt ).down( 'img' ).alt == 'Étendre' ) {
				$( elmt ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				$( elmt ).down( 'img' ).alt = 'Réduire';
			}
			else {
				$( elmt ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				$( elmt ).down( 'img' ).alt = 'Étendre';
			}
		} );

		$( elmt ).insert( { 'top' : biglink } );
	});

}

function OpenTree(action, absoluteBaseUrl, large) {
	var dir = absoluteBaseUrl + 'img/icons';
	$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
		if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {
			var thisTr = $( elmtTd ).up( 'tr' );
			if( action == 'open' ) {
				$( elmtTd ).down( 'a' ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				$( elmtTd ).down( 'a' ).down( 'img' ).alt = 'Réduire';
				var nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.show();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
			else {
				$( elmtTd ).down( 'a' ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				$( elmtTd ).down( 'a' ).down( 'img' ).alt = 'Étendre';
				var nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.hide();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
		}
	} );
}

// Fonction non-prototype commune

function printit(){
	if (window.print) {
		window.print() ;
	} else {
		var WebBrowser = '<object id="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>';
		document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
		WebBrowser1.ExecWB(6, 2);//Use a 1 vs. a 2 for a prompting dialog box    WebBrowser1.outerHTML = "";
	}
}



/*
*   Title :     charcount.js
*   Author :        Terri Ann Swallow
*   URL :       http://www.ninedays.org/
*   Project :       Ninedays Blog
*   Copyright:      (c) 2008 Sam Stephenson
*               This script is is freely distributable under the terms of an MIT-style license.
*   Description :   Functions in relation to limiting and displaying the number of characters allowed in a textarea
*   Version:        2.1
*   Changes:        Added overage override.  Read blog for updates: http://blog.ninedays.org/2008/01/17/limit-characters-in-a-textarea-with-prototype/
*   Created :       1/17/2008 - January 17, 2008
*   Modified :      5/20/2008 - May 20, 2008
*
*   Functions:      init()                      Function called when the window loads to initiate and apply character counting capabilities to select textareas
*   charCounter(id, maxlimit, limited)  Function that counts the number of characters, alters the display number and the calss applied to the display number
*   makeItCount(id, maxsize, limited)   Function called in the init() function, sets the listeners on teh textarea nd instantiates the feedback display number if it does not exist
*/

function textareaCharCounter(id, maxlimit, limited){
	if (!$('counter-'+id)){
		$(id).insert({after: '<div id="counter-'+id+'"></div>'});
		}
	if($F(id).length >= maxlimit){
		if(limited){    $(id).value = $F(id).substring(0, maxlimit); }
		$('counter-'+id).addClassName('charcount-limit');
		$('counter-'+id).removeClassName('charcount-safe');
	} else {
		$('counter-'+id).removeClassName('charcount-limit');
		$('counter-'+id).addClassName('charcount-safe');
	}
	$('counter-'+id).update( $F(id).length + '/' + maxlimit );
}

function textareaMakeItCount(textareaId, maxsize, limited){
	if(limited == null) limited = true;
	if ($(textareaId)){
		Event.observe($(textareaId), 'keyup', function(){textareaCharCounter(textareaId, maxsize, limited);}, false);
		Event.observe($(textareaId), 'keydown', function(){textareaCharCounter(textareaId, maxsize, limited);}, false);
		textareaCharCounter(textareaId,maxsize,limited);
	}
}

// http://jehiah.cz/a/firing-javascript-events-properly
function fireEvent(element,event) {
	if (document.createEventObject) {// dispatch for IE
		var evt = document.createEventObject();
		return element.fireEvent('on'+event,evt)
	}
	else { // dispatch for firefox + others
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent(event, true, true ); // event type,bubbling,cancelable
		return !element.dispatchEvent(evt);
	}
}

/**
 * Vérifie si une valeur existe dans un array.
 *
 * @url http://snippets.dzone.com/posts/show/4653
 *
 * @param {String|Number} p_val La valeur à rechercher
 * @param {Array} p_array L'array dans laquelle rechercher la valeur
 * @param {Boolean} p_suffix true s'il faut rechercher en tant que suffixe
 *	(false par défaut)
 * @returns {Boolean}
 */
function in_array(p_val, p_array, p_suffix) {
	var re = new RegExp( '_' + p_val + '$' );
	p_suffix = ( typeof p_suffix != 'undefined' ) ? p_suffix : false;

	for(var i = 0, l = p_array.length; i < l; i++) {
		if(p_array[i] == p_val || ( p_suffix && String(p_array[i]).match(re) ) ) {
			return true;
		}
	}
	return false;
}

/**
* Fonction pour la visualisation des décisions des EPs (app/views/commissionseps/decisionXXXX.ctp)
*
* @param string idColumnToChangeColspan L'id de la colonne qui s'étendra sur les colonnes à masquer
* @param string decision La décision courante
* @param integer colspanMax Le nombre de colonnes à masquer
* @param array idsNonRaisonpassage Les ids des colonnes à masquer
* @param array decisionsHide Les valeurs de decision entraînant un masquage
*/

function changeColspanViewInfosEps( idColumnToChangeColspan, decision, colspanMax, idsNonRaisonpassage, decisionsHide ) {
	decisionsHide = typeof(decisionsHide) != 'undefined' ? decisionsHide : [ 'reporte', 'annule', 'maintienref', 'refuse', 'suspensionnonrespect', 'suspensiondefaut'/*, 'maintien'*/ ];

	if ( in_array( decision, decisionsHide ) ) {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", colspanMax );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).hide();
		});
	}
}

/*
* Fonction pour afficher/masquer les champs de décision complémentaires pour les EPs (app/views/commissionseps/traiterXXXX.ctp)
*/

function changeColspanFormAnnuleReporteEps( idColumnToChangeColspan, colspanMax, decision, idsNonRaisonpassage ) {
	if ( $(idColumnToChangeColspan) === null ) {
		throw idColumnToChangeColspan + " element not found!";
	}

	if ( $F( decision ) == 'reporte' || $F( decision ) == 'annule' ) {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", colspanMax );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).disable().up(1).hide();
		});
	}
	else {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", 1 );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).enable().up(1).show();
		});
	}
}

/**
* Permet de cocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/

function toutCocher( selecteur, simulate ) {
	if( selecteur == undefined ) {
		selecteur = 'input[type="checkbox"]';
	}

	$$( selecteur ).each( function( checkbox ) {
		if( simulate != true ) {
			$( checkbox ).checked = true;
		}
		else if( $( checkbox ).checked != true ) {
			$( checkbox ).simulate( 'click' );
		}
	} );

	return false;
}

/**
* Permet de décocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/

function toutDecocher( selecteur, simulate ) {
	if( selecteur == undefined ) {
		selecteur = 'input[type="checkbox"]';
	}

	$$( selecteur ).each( function( checkbox ) {
		if( simulate != true ) {
			$( checkbox ).checked = false;
		}
		else if( $( checkbox ).checked != false ) {
			$( checkbox ).simulate( 'click' );
		}
	} );

	return false;
}

/**
* Ajout les boutons "Tout cocher" et "Tout décocher" en haut d'un élément.
*
* @param elmt L'élément en haut duquel les bouton seront ajoutés
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
*/
function insertButtonsCocherDecocher( elmt, selecteur ) {
	elmt = $(elmt);

	if( undefined !== elmt ) {
		if( selecteur == undefined ) {
			selecteur = 'input[type="checkbox"]';
		}

		elmt.insert( {
			top: new Element(
				'button', {
					type: 'button',
					onclick: "return toutCocher( '" + selecteur + "' );"
				} ).update( 'Tout cocher' ).outerHTML
				+ new Element(
				'button', {
					type: 'button',
					onclick: "return toutDecocher( '" + selecteur + "' );"
				} ).update( 'Tout décocher' ).outerHTML
		} );
	}
}

/**
 * Active et affiche une partie d'un formulaire contenu dans une balise
 */

function enableAndShowFormPart( formpartid ) {
	$( formpartid ).removeClassName( 'disabled' );
	$( formpartid ).show();

	$( formpartid ).getElementsBySelector( 'div.input' ).each( function( elmt ) {
		$( elmt ).removeClassName( 'disabled' );
	} );

	$( formpartid ).getElementsBySelector( 'input', 'select', 'button', 'textarea', 'radio' ).each( function( elmt ) {
		// INFO: elmt.enable() ne fonctionne pas avec des button
		try{
			elmt.enable();
		} catch( err ) {
			elmt.disabled = false;
		}
	} );
}

/**
 * Désactive et cache une partie d'un formulaire contenu dans une balise
 */

function disableAndHideFormPart( formpartid ) {
	$( formpartid ).addClassName( 'disabled' );
	$( formpartid ).hide();

	$( formpartid ).getElementsBySelector( 'div.input' ).each( function( elmt ) {
		$( elmt ).addClassName( 'disabled' );
	} );

	$( formpartid ).getElementsBySelector( 'input', 'select', 'button', 'textarea', 'radio' ).each( function( elmt ) {
		// INFO: elmt.disable() ne fonctionne pas avec des button
		try{
			elmt.disable();
		} catch( err ) {
			elmt.disabled = true;
		}
	} );
}

/**
 * Marque les li correspondant aux onglets en erreur (classe error) lorsqu'ils
 * comportent une balise en erreur.
 */
function makeErrorTabs() {
	$$( '.error' ).each( function( elmt ) {
		$(elmt).ancestors().each( function( ancestor ) {
			if( $(ancestor).hasClassName( 'tab' ) ) {
				$$( 'a[href=#' + $(ancestor).readAttribute( 'id' ) + ']' ).each( function( tabLink ) {
					$(tabLink).up( 'li' ).addClassName( 'error' );
				} );
			}
		} );
	} );
}

/**
 * Fonction permettant de filtrer les options d'un select à partir de la valeur
 * d'un radio.
 * Une option avec une valeur vide est toujours conservée.
 * Lorsque le select valait une des valeurs que l'on cache, sa valeur devient
 * la chaîne vide.
 *
 * Exemple:
 * <pre>
 * filterSelectOptionsFromRadioValue(
 *		'FormHistochoixcer93',
 *		'data[Histochoixcer93][formeci]',
 *		'Histochoixcer93Decisioncs',
 *		{
 *			'S': ['valide', 'aviscadre'],
 *			'C': ['aviscadre', 'passageep']
 *		}
 * );
 * </pre>
 *
 * @param string formId
 * @param string radioName
 * @param string selectId
 * @param hash values
 */
function filterSelectOptionsFromRadioValue( formId, radioName, selectId, values ) {
	var v = $( formId ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	var accepted = values[currentValue];

	$$('#' + selectId + ' option').each( function ( option ) {
		if( option.value != '' ) {
			if( in_array( option.value, accepted ) ) {
				option.show();
			}
			else {
				option.hide();
			}
		}
	} );

	var currentSelectValue = $F( selectId );
	if( currentSelectValue != '' && !in_array( currentSelectValue, accepted ) ) {
		$( selectId ).value = '';
	}
}

/**
 * Fonction permettant de d'observer le changement de valeur d'un radio et de
 * filtrer les options d'un select à partir de sa valeur.
 *
 * Exemple:
 * <pre>
 * observeFilterSelectOptionsFromRadioValue(
 *		'FormHistochoixcer93',
 *		'data[Histochoixcer93][formeci]',
 *		'Histochoixcer93Decisioncs',
 *		{
 *			'S': ['valide', 'aviscadre'],
 *			'C': ['aviscadre', 'passageep']
 *		}
 * );
 * </pre>
 *
 * @see filterSelectOptionsFromRadioValue()
 *
 * @param string formId
 * @param string radioName
 * @param string selectId
 * @param hash values
 */
function observeFilterSelectOptionsFromRadioValue( formId, radioName, selectId, values ) {
	filterSelectOptionsFromRadioValue( formId, radioName, selectId, values );

	var v = $( formId ).getInputs( 'radio', radioName );
	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			filterSelectOptionsFromRadioValue( formId, radioName, selectId, values );
		} );
	} );
}

/**
 * Retourne la valeur d'un radio présent au sein d'un formulaire particulier
 *
 * @param string form L'id du formulaire (ex.: 'contratinsertion')
 * @param string radioName Le name du radio (ex.: 'data[Cer93][duree]')
 * @return string
 */
function radioValue( form, radioName ) {
	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	return currentValue;
}

/**
* Permet de cocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/
function toutChoisir( radios, valeur, simulate ) {
		$( radios ).each( function( radio ) {
			if( radio.value == valeur ) {
				if( simulate != true ) {
					$( radio ).writeAttribute("checked", "checked");
				}
				else {
					$( radio ).simulate( 'click' );
				}
			}
		} );

	return false;
}

//-----------------------------------------------------------------------------

/**
 * Transforme les liens ayant la classe "external" pour qu'ils s'ouvrent dans
 * une nouvelle fenêtre (un nouvel onglet) via Javascript.
 *
 * @return void
 */
function make_external_links() {
	$$('a.external').each( function ( link ) {
		var originalJavascript = $( link ).onclick;

		$( link ).onclick = function() {
			var result = true;

			if( null !== originalJavascript ) {
				result = originalJavascript();
			}

			if( true === result ) {
				window.open( $( link ).href, '_blank' );
			}

			return false;
		};
	} );
}

//-----------------------------------------------------------------------------

/**
 * Retourne les éléments de formulaires sérialisés d'une des lignes d'un tableau
 * (la ligne qui contient le lien Ajax passé en paramètre).
 *
 * Les éléments de formulaire doivent impérativement se trouver entre des balises
 * <form>...</form>
 *
 * @param Un sélecteur vers le lien Ajax permettant d'envoyer la ligne.
 * @return string
 */
function serializeTableRow( link ) {
	var form = $(link).up( 'form' );
	var trId = $(link).up('tr').id;

	return Form.serializeElements(
		$( form )
		.getElementsBySelector(
			'#' + trId + ' input',
			'#' + trId + ' select',
			'#' + trId + ' textarea'
		)
	);
}

/**
 * Fonction permettant d'éviter qu'un formulaire ne soit envoyé plusieurs fois.
 * Utilisée notamment pour la connexion.
 *
 * @param formId Le formulaire sur lequel appliquer la fonctionnalité
 * @param message Le message à afficher au-dessus du formulaire pour tenir l'utilisateur informé.
 */
function observeDisableFormOnSubmit( formId, message ) {
	message = typeof(message) != 'undefined' ? message : null;

	var submits = $(formId).getElementsBySelector( '*[type=submit]' );

	if( typeof submits !== 'undefined' ) {
		$(submits).each( function( submit ) {
			if( typeof $(submit).name === 'string' && $(submit).name.length > 0 ) {
				Event.observe(
					$(submit),
					'click',
					// Si le formulaire a été envoyé via un bouton, on l'ajoute aux données envoyées
					function( event ) {
						var name = 'data[' + $(submit).name + ']';

						// Si d'autres éléments du même nom existent, on les supprime
						$(formId).select( 'input[name="' + name + '"]' ).each( function( old ) {
							$(old).remove();
						} );

						var hidden = new Element(
							'input',
							{
								type: 'hidden',
								name: name,
								value: $(submit).value,
							}
						);
						$(formId).insert( { 'top' : hidden } );
					}
				);
			}
		} );
	}

	Event.observe(
		formId,
		'submit',
		function( submitter ) {
			// Ajout de l'enventuel message en haut du formaulire
			if( typeof(message) !== 'undefined' && message !== null ) {
				var notice = new Element( 'p', { 'class': 'notice' } ).update( message );
				$( formId ).insert( { 'top' : notice } );
			}

			// Désactivation des boutons
			$$( '#' + formId + ' *[type=submit]', '#' + formId + ' *[type=reset]' ).each( function( submit ) {
				$( submit ).disabled = true;
			} );
		}
	);
}



/**
 * Pour chacun des liens trouvés par le chemin, on remplace la partie signet
 * (#...) existante par le signet passé en paramètre.
 *
 * @param String links Le chemin des liens à modifier
 * @param String fragment Le signet à utiliser pour la modification
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function replaceUrlFragments( links, fragment, prefix ) {
	prefix = 'undefined' === typeof prefix ? null : prefix + ',';

	$$( links ).each( function( link ) {
		var href = $(link).readAttribute( 'href' );
		if(null !== prefix) {
			fragment = fragment.replace( /^#/, '#' + prefix );
		}
		href = href.replace( /#.*$/, '' ) + fragment;
		$(link).writeAttribute( 'href', href );
	} );
}

/**
 * Observe l'événement 'onclick' de chacun des liens du premier chemin, qui ne
 * doivent être composés que de signet (#nomsignet), pour modifier les signets
 * des liens du second chemin.
 *
 * @param String observedPath Le chemin des liens à observer
 * @param String replacedPath Le chemin des liens pour lesquels modifier le signet
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function observeOnclickUrlFragments( observedPath, replacedPath, prefix ) {
	$$( observedPath ).each( function( observed ) {
		$(observed).observe(
			'click',
			function() {
				replaceUrlFragments( replacedPath, $(observed).readAttribute( 'href' ), prefix );
			}
		);
	} );
}

/**
 * Observe l'événement 'onload' de la page pour modifier les liens du chemin en
 * fonction de la dernière partie du signet (#dossiers,propononorientationprocov58
 * donnera #propononorientationprocov58) présent dans l'URL de la page.
 *
 * @param String replacedPath Le chemin des liens pour lesquels modifier le signet
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function observeOnloadUrlFragments( replacedPath, prefix ) {
	document.observe( "dom:loaded", function() {
		if( window.location.href.indexOf( '#' ) !== -1 ) {
			var fragment = window.location.href.replace( /^.*#/, '#' ).replace( /^.*,([^,]+$)/g, '#$1' );
			replaceUrlFragments( replacedPath, fragment, prefix );
		}
	} );
}

/**
 * Retourne le nombre de jours séparant deux dates.
 *
 * @url http://www.htmlgoodies.com/html5/javascript/calculating-the-difference-between-two-dates-in-javascript.html#fbid=WAI_I5iVM_N
 *
 * @param Date date1 La date la plus ancienne
 * @param Date date2 La date la plus récente
 * @return int
 */
function nbJoursIntervalleDates( date1, date2 ) {
	//Get 1 day in milliseconds
	var one_day=1000*60*60*24;

	// Convert both dates to milliseconds
	var date1_ms = date1.getTime();
	var date2_ms = date2.getTime();

	// Calculate the difference in milliseconds
	var difference_ms = date2_ms - date1_ms;

	// Convert back to days and return
	return Math.round(difference_ms/one_day);
}

/**
 * Met à jour un champ avec le nombre de jours entre deux dates (constituées de SELECT CakePHP).
 *
 * @param string date1 Le préfix du champ date la plus ancienne
 * @param string date2 Le préfix du champ date la plus récente
 * @param string fieldId L'id de l'élément à mettre à jour
 * @return void
 */
function updateFieldFromDatesInterval( date1, date2, fieldId ) {
	var dateComplete1 = ( $F( date1 + 'Day' ) && $F( date1 + 'Month' ) && $F( date1 + 'Year' ) );
	var dateComplete2 = ( $F( date2 + 'Day' ) && $F( date2 + 'Month' ) && $F( date2 + 'Year' ) );

	if( dateComplete1 && dateComplete2 ) {
		var n = nbJoursIntervalleDates(
			new Date( $F( date1 + 'Year' ), $F( date1 + 'Month' ), $F( date1 + 'Day' ) ),
			new Date( $F( date2 + 'Year' ), $F( date2 + 'Month' ), $F( date2 + 'Day' ) )
		);
		$( fieldId ).update( n );
	}
}

/**
 * Met à jour une date de fin à partir d'une date de début et d'une durée.
 *
 * @param string date1 Le nom du champ de date de début (sera suffixé à la manière CakePHP)
 * @param string duree Le nom du champ de durée
 * @param string date2 Le nom du champ de date de fin (sera suffixé à la manière CakePHP)
 * @return void
 */
function updateDateFromDateDuree( date1, duree, date2 ) {
	var complete = (
		( $F( date1 + 'Year' ) && $F( date1 + 'Month' ) && $F( date1 + 'Day' ) )
		&& $F( duree )

	);

	if( complete ) {
		setDateInterval( date1, date2, $F( duree ), false );
	}
}


// http://phpjs.org/functions/strtotime/
 function strtotime (text, now) {
     // Convert string representation of date and time to a timestamp
     //
     // version: 1109.2015
     // discuss at: http://phpjs.org/functions/strtotime
     // +   original by: Caio Ariede (http://caioariede.com)
     // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     // +      input by: David
     // +   improved by: Caio Ariede (http://caioariede.com)
     // +   improved by: Brett Zamir (http://brett-zamir.me)
     // +   bugfixed by: Wagner B. Soares
     // +   bugfixed by: Artur Tchernychev
     // +   improved by: A. Matías Quezada (http://amatiasq.com)
     // %        note 1: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
     // *     example 1: strtotime('+1 day', 1129633200);
     // *     returns 1: 1129719600
     // *     example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
     // *     returns 2: 1130425202
     // *     example 3: strtotime('last month', 1129633200);
     // *     returns 3: 1127041200
     // *     example 4: strtotime('2009-05-04 08:30:00');
     // *     returns 4: 1241418600
     if (!text)
         return null;

     // Unecessary spaces
     text = text.trim()
         .replace(/\s{2,}/g, ' ')
         .replace(/[\t\r\n]/g, '')
         .toLowerCase();

     var parsed;

     if (text === 'now')
         return now === null || isNaN(now) ? new Date().getTime() / 1000 | 0 : now | 0;
     else if (!isNaN(parse = Date.parse(text)))
         return parse / 1000 | 0;
     if (text === 'now')
         return new Date().getTime() / 1000; // Return seconds, not milli-seconds
     else if (!isNaN(parsed = Date.parse(text)))
         return parsed / 1000;

     var match = text.match(/^(\d{2,4})-(\d{2})-(\d{2})(?:\s(\d{1,2}):(\d{2})(?::\d{2})?)?(?:\.(\d+)?)?$/);
     if (match) {
         var year = match[1] >= 0 && match[1] <= 69 ? +match[1] + 2000 : match[1];
         return new Date(year, parseInt(match[2], 10) - 1, match[3],
             match[4] || 0, match[5] || 0, match[6] || 0, match[7] || 0) / 1000;
     }

     var date = now ? new Date(now * 1000) : new Date();
     var days = {
         'sun': 0,
         'mon': 1,
         'tue': 2,
         'wed': 3,
         'thu': 4,
         'fri': 5,
         'sat': 6
     };
     var ranges = {
         'yea': 'FullYear',
         'mon': 'Month',
         'day': 'Date',
         'hou': 'Hours',
         'min': 'Minutes',
         'sec': 'Seconds'
     };

     function lastNext(type, range, modifier) {
         var day = days[range];

         if (typeof(day) !== 'undefined') {
             var diff = day - date.getDay();

             if (diff === 0)
                 diff = 7 * modifier;
             else if (diff > 0 && type === 'last')
                 diff -= 7;
             else if (diff < 0 && type === 'next')
                 diff += 7;

             date.setDate(date.getDate() + diff);
         }
     }
     function process(val) {
         var split = val.split(' ');
         var type = split[0];
         var range = split[1].substring(0, 3);
         var typeIsNumber = /\d+/.test(type);

         var ago = split[2] === 'ago';
         var num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

         if (typeIsNumber)
             num *= parseInt(type, 10);

         if (ranges.hasOwnProperty(range))
             return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
         else if (range === 'wee')
             return date.setDate(date.getDate() + (num * 7));

         if (type === 'next' || type === 'last')
             lastNext(type, range, num);
         else if (!typeIsNumber)
             return false;

         return true;
     }

     var regex = '([+-]?\\d+\\s' +
         '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' +
         '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' +
         '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday)|(last|next)\\s' +
         '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' +
         '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' +
         '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday))(\\sago)?';

     match = text.match(new RegExp(regex, 'gi'));
     if (!match)
         return false;

     for (var i = 0, len = match.length; i < len; i++)
         if (!process(match[i]))
             return false;

     // ECMAScript 5 only
     //if (!match.every(process))
     //	return false;

     return (date.getTime() / 1000);
 }

// -----------------------------------------------------------------------------
// Fonctions "AjaxAction" utilisées par la méthode PrototypeAjaxHelper::observe()
// -----------------------------------------------------------------------------

/**
 * Permet de récupérer les valeurs de certains champs de formulaire dont les
 * id (au sens HTML) se trouvent dans l'Array parameters.fields.
 *
 * On peut forcer des valeurs qui ne sont pas encore remplies dans le formulaire
 * (par exemple au chargement de la page) dès lors que l'Array parameters.values
 * contient des id en "clé" et les valeurs à forcer en "valeur".
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- full (boolean): permet de choisir sous quel format les paramètres post seront envoyés)
 *	- fields (Array): une liste d'id (HTML) de champs à envoyer
 *	- values (objet): une liste d'attributs id (HTML) / valeur à forcer pour les champs à envoyer
 * }}}
 *
 * @param object parameters
 * @returns object
 */
function cake_data(parameters) {
	var fields = parameters.fields;
	var data = {};

	parameters.fields.each( function(input) {
		if( typeof parameters.full !== 'undefined' && parameters.full ) {
			data[$(input).name] = {
				'domId': $(input).id,
				'name': $(input).name,
				'type': $(input).type,
				'value': $F(input)
			};
		}
		else {
			data[$(input).name] = $F(input);
		}
	} );

	// Possède-t'on des valeurs "forcées"
	if( typeof parameters.values === 'object' ) {
		for( domId in parameters.values ) {
			var value = parameters.values[domId];

			if( typeof parameters.full !== 'undefined' && parameters.full ) {
				data[$(domId).name]['value'] = value;
			}
			else {
				data[$(domId).name] = value;
			}
		};
	}

	return data;
}


/**
 * "Surcharge" de la classe Ajax.Updater pour s'assurer de n'avoir que seule la
 * dernière requête d'updater pour une URL soit prise en compte.
 *
 * Lorsqu'une requête précédente est trouvée, elle est annulée lors du lancement
 * de la nouvelle requête.
 *
 * La liste des updaters en cours est stockée dans windows.updaters (Hash).
 */
Ajax.AbortableUpdater = Class.create(
	Ajax.Updater,
	{
		initialize: function( $super, container, url, options ) {
			var key = url;

			// Création du dictionnaire associatif des updaters
			if( typeof window.updaters === 'undefined' ) {
				window.updaters = new Hash();
			}

			// Annulation de l'updater précédent
			var previous = window.updaters.get( key );
			if( typeof previous !== 'undefined' ) {
				previous.transport.abort();
				window.updaters.unset( key );
			}

			// "Surcharge" de la méthode onComplete des options
			options = Object.clone(options);
			var onComplete = options.onComplete;
			options.onComplete = ( function( response, json) {
				if( Object.isFunction( onComplete ) ) onComplete( response, json );

				// Suppression de la référence à l'updater
				window.updaters.unset( key );
			} ).bind( this );

			$super( container, url, options );

			// Sauvegarde de la référence à l'updater
			window.updaters.set( key, this );
		}
	}
);

/**
 * "Surcharge" de la classe Ajax.Request pour s'assurer de n'avoir que seule la
 * dernière requête pour une URL soit prise en compte.
 *
 * Lorsqu'une requête précédente est trouvée, elle est annulée lors du lancement
 * de la nouvelle requête.
 *
 * La liste des requests en cours est stockée dans windows.requests (Hash).
 */
Ajax.AbortableRequest = Class.create(
	Ajax.Request,
	{
		initialize: function( $super, url, options ) {
			var key = url;

			// Création du dictionnaire associatif des requests
			if( typeof window.requests === 'undefined' ) {
				window.requests = new Hash();
			}

			// Annulation de la request précédent
			var previous = window.requests.get( key );
			if( typeof previous !== 'undefined' ) {
				previous.transport.abort();
				window.requests.unset( key );
			}

			// "Surcharge" de la méthode onComplete des options
			options = Object.clone(options);
			var onComplete = options.onComplete;
			options.onComplete = ( function( response, json) {
				if( Object.isFunction( onComplete ) ) onComplete( response, json );

				// Suppression de la référence à la request
				window.requests.unset( key );
			} ).bind( this );

			$super( url, options );

			// Sauvegarde de la référence à la request
			window.requests.set( key, this );
		}
	}
);

/**
 * Valeurs de keyCode à ne pas prendre en compte pour les champs de type Ajax
 * autocomplete.
 *
 * @url http://www.javascripter.net/faq/keycodes.htm
 */
var unobservedKeys = [
	Event.KEY_TAB,
	Event.KEY_RETURN,
	Event.KEY_ESC,
	Event.KEY_LEFT,
	Event.KEY_UP,
	Event.KEY_RIGHT,
	Event.KEY_DOWN,
	Event.KEY_HOME,
	Event.KEY_END,
	Event.KEY_PAGEUP,
	Event.KEY_PAGEDOWN,
	Event.KEY_INSERT,
	16, // shift
	17, // ctrl
	18, // alt
	19, // pause (FF)
	20, // caps lock
	42, //PrntScrn (FF)
	44, //PrntScrn
	91, // 91
	112, // F1
	113, // F2
	114, // F3
	115, // F4
	116, // F5
	117, // F6
	118, // F7
	119, // F8
	120, // F9
	121, // F10
	122, // F11
	123, // F12
	144, // NumLock
	145 // ScrollLock
];

/**
 * La méthode de callback (par défaut) lancée par le onSuccess de l'appel
 * Ajax.Request de la fonction ajax_action.
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- url (string): l'URL qui a été utilisée lors de l'appel Ajax post
 *	- data[Target][name] (string): dans le cas de l'événement click, l'attribut name du champ texte ayant servi au champ "autocomplete"
 * }}}
 *
 * @param object response
 * @param object parameters
 * @returns void
 */
function ajax_action_on_success(response, parameters) {
	var json = response.responseText.evalJSON(true);

	if( json.success ) {
		for( path in json.fields ) {
			try {
				var field = json.fields[path];
				// Test: $(field), $(field).type, $(field).id, $(field).value, $(field).options

				if( $(field).type === 'select' ) {
					if( typeof $(field).options !== 'undefined' ) {
						var select = new Element( 'select' );
						$(select).insert( { bottom: new Element( 'option', { 'value': '' } ) } );

						var options = $(field).options;
						if( $(options) != [] ) {
							$(options).each( function( result ) {
								var title = ( typeof $(result).title === 'undefined' ? '' : $(result).title );
								var option = Element( 'option', { 'value': $(result).id, 'title': title } ).update( $(result).name );
								$(select).insert( { bottom: option } );
							} );
						}

						$($(field).id).update( $(select).innerHTML );
					}
				}
				else if( $(field).type === 'ajax_select' ) {

					var domIdSelect = $(field).id + 'AjaxSelect';
					var oldAjaxSelect = $( domIdSelect );
					if( oldAjaxSelect ) {
						$( oldAjaxSelect ).remove();
					}

					if( typeof $(field).options !== 'undefined' && $($(field).options).length > 0 ) {
						var ajaxSelect = new Element( 'ul' );

						$($(field).options).each( function ( result ) {
							var a = new Element( 'a', { href: '#', onclick: 'return false;' } ).update( result.name );

							$( a ).observe( 'click', function( event ) {
								$( domIdSelect ).remove();

								var params = {
									'data[Event][type]': 'click',
									'data[id]': $(field).id,
									'data[name]': parameters['data[Target][name]'],
									'data[value]': $(result).id,
									'data[prefix]': $(field).prefix
								};

								new Ajax.AbortableRequest(
									parameters.url,
									{
										method: 'post',
										parameters: params,
										onSuccess: function( response ) {
											ajax_action_on_success( response, params );
										}
									}
								);

								return false;
							} );

							$( ajaxSelect ).insert( { bottom: $( a ).wrap( 'li' ) } );
						} );

						$( $(field).id ).up( 'div' ).insert(  { after: $( ajaxSelect ).wrap( 'div', { 'id': domIdSelect, 'class': 'ajax select' } ) }  );
					}
				}

				// On ne modifie / renvoie pas systématiquement la valeur des champs
				if( typeof $(field).value !== 'undefined' ) {
					// Si c'est une case à cocher, voir si on doit cocher / décocher
					if( $($(field).id).type === 'checkbox' ) {
						var before = $($(field).id).checked;
						var after = !( $(field).value === null || $(field).value === '' || $(field).value == '0' );

						if( before !== after ) {
							$( $($(field).id) ).simulate( 'click' );
						}
					}
					else {
						$($(field).id).value = $(field).value;

						if( $(field).simulate === true ) {
							$($(field).id).simulate( 'change' );
						}
					}
				}
			} catch( Exception ) {
				console.log( Exception );
			}
		}

		// Événements à lancer ?
		if( typeof json.events === 'object' && json.events.length > 0 ) {
			$(json.events).each( function ( customEvent ) {
				Event.fire( document, customEvent );
			} );
		}
	}
}

/**
 * Effectue un appel Ajax post "à la mode CakePHP" (grâce à la méthode cake_data())
 * suite au déclenchement d'un événement.
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- url (string): l'URL (relative ou absolue) à appeler en Ajax.
 *	- prefix (string): le préfixe utilisé dans les id et name (HTML) des champs
 *	- full (boolean): permet de choisir sous quel format les paramètres post seront envoyés (@see cake_data())
 *	- fields (Array): une liste d'id (HTML) de champs à envoyer
 *	- values (objet): une liste d'attributs id (HTML) / valeur à forcer pour les champs à envoyer
 *	- delay (integer): le nombre de millisecondes de délai à utiliser avant l'envoi lorsque l'événement est de type keyup ou keydown. Par défaut: 500.
 *	- min (integer): le nombre minimum de caractères devant être remplis lorsque l'événement est de type keyup ou keydown. Par défaut: 3.
 * }}}
 *
 * En cas de succès de Ajax.Request, la fonction de rappel ajax_action_on_success()
 * sera appelée.
 *
 * Les paramètres post ajoutés par la méthode sont:
 * {{{
 *	- data[Event][type]: le type d'événement (dataavailable, keyup, keydown, change, ...)
 *	- data[Target][domId]: l'id (HTML) de l'élément qui a déclenché l'événement (non rempli lorsque l'événement dataavailable)
 *	- data[Target][name]: le name (HTML) de l'élément qui a déclenché l'événement (non rempli lorsque l'événement dataavailable)
 * }}}
 *
 * Si la même requête (url, prefix) est déjà en cours, on l'annule.
 *
 * @param Event event L'événement qui a déclenché l'appel à la fonction.
 * @param object parameters
 * @returns void
 */
function ajax_action( event, parameters ) {
	var keyEvents = [ 'keyup', 'keydown' ],
		doAjaxAction = function( event, parameters ) {
		var postParams = cake_data( parameters );

		postParams['data[prefix]'] = parameters.prefix;
		postParams['data[Event][type]'] = $(event).type;

		var element = $(event).element(); // Dans les cas du change et du keyup
		postParams['data[Target][domId]'] = $(element).id;
		postParams['data[Target][name]'] = $(element).name;

		new Ajax.AbortableRequest(
			parameters.url,
			{
				parameters: postParams,
				onSuccess: function( response ) {
					postParams.url = parameters.url;
					ajax_action_on_success( response, postParams );
					if( in_array( $(event).type, keyEvents ) && !in_array( event.keyCode, unobservedKeys ) ) {
						element.removeClassName( 'loading' );
						delete window.ajax_timeout_queue[event.type][element.id];
					}
				}
			}
		);
	};

	// Si ce n'est pas un événement de type keyup/keydown sur un champ texte
	if( !in_array( event.type, keyEvents ) ) {
		doAjaxAction( event, parameters );
	}
	// Si c'est un événement de type keyup/keydown sur un champ texte
	else if( !in_array( event.keyCode, unobservedKeys ) ) {
		var element = $(event).element(),
			delay = ( parameters.delay === undefined ? 500 : parameters.delay ),
			min = ( parameters.min === undefined ? 3 : parameters.min );

		// Liste globale des timeouts
		window.ajax_timeout_queue = ( window.ajax_timeout_queue === undefined ? {} : window.ajax_timeout_queue );
		window.ajax_timeout_queue[event.type] = ( window.ajax_timeout_queue[event.type] === undefined ? {} : window.ajax_timeout_queue[event.type] );

		// Si on a un nombre de lettres suffisant
		if( $F(element).length >= min ) {
			element.addClassName( 'loading' );

			if( window.ajax_timeout_queue[event.type][element.id] !== undefined ) {
				clearTimeout( window.ajax_timeout_queue[event.type][element.id] );
				delete window.ajax_timeout_queue[event.type][element.id];
			}

			window.ajax_timeout_queue[event.type][element.id] = setTimeout(
				function() { doAjaxAction( event, parameters ); },
				delay
			);
		}
		// Sinon, on nettoie le timeout, la classe, la liste de résultats
		else {
			if( window.ajax_timeout_queue[event.type][element.id] !== undefined ) {
				clearTimeout( window.ajax_timeout_queue[event.type][element.id] );
				delete window.ajax_timeout_queue[event.type][element.id];
			}

			element.removeClassName( 'loading' );

			var oldAjaxSelect = $( $(element).id + 'AjaxSelect' );
			if( oldAjaxSelect ) {
				$( oldAjaxSelect ).remove();
			}
		}
	}
}

/**
 * Permet de tronquer la longueur du texte d'un élément à la valeur demandée avec
 * ajout de l'ellipse à la fin et mise en attribut title de l'élément du texte
 * complet lorsque le texte dans la balise doit être tronqué.
 *
 * @param string|Element tag
 * @param integer maxLength
 * @param string ellipsis
 * @returns void
 */
function truncateWithEllipsis( tag, maxLength, ellipsis ) {
	maxLength = typeof(maxLength) != 'undefined' ? maxLength : 100;
	ellipsis = typeof(ellipsis) != 'undefined' ? ellipsis : '...';

	var oldTitle = $(tag).innerHTML;
	if( oldTitle.length > maxLength ) {
		var newTitle = oldTitle.substr( 0, maxLength - ellipsis.length ) + ellipsis;
		$(tag).update( newTitle );
		$(tag).writeAttribute( 'title', oldTitle );
	}
}

/**
 * Permet de séparer la partie date de la partie time d'un champ datetime
 * généré par CakePHP avec la chaîne de caractères spécifiée en paramètre.
 *
 * @param string id L'id de base du champ datetime. Par exemple: ModeField
 * @param string text La chaîne servant de séparateur. Par défaut: ' à '
 * @returns void
 */
function cakeDateTimeSeparator( id, text ) {
	if( typeof text === 'undefined' ) {
		text = ' à ';
	}

	try {
		var hour = $( id + 'Hour' );
		var oldDatetimeSeparators = $( hour ).up( 'div.input' ).down( 'span.datetime_separator' );

		if( typeof $(oldDatetimeSeparators) !== 'undefined' ) {
			$(oldDatetimeSeparators).each( function ( datetimeSeparator ) {
				$(datetimeSeparator).remove();
			} );
		}
		var span = new Element( 'span', { 'class': 'datetime_separator' } ).update( text );
		$( hour ).insert( { 'before' : span } );
	} catch( Exception ) {
		console.log( Exception );
	}
}

/**
 * Permet de récupérer le nombre de requêtes SQL se trouvant dans la table de
 * classe cake-sql-log générée par CakePHP lorsque debug > 0.
 *
 * @returns {Number}
 */
function getCakeQueriesCount() {
	var count = 0;

	$$( 'table.cake-sql-log' ).each( function( table ) {
		count += table.rows.length - 1;
	} );

	return count;
}

/**
 * Objet contenant des méthodes utilitaires pouvant être utilisées dans la prise
 * de décision de différentes thématiques de COV et d'EP.
 *
 * @namespace Commission
 */
var Commission = {
	myTriggerEvent: function( id, name ) {
		try {
			Element.getStorage( id )
				.get( 'prototype_event_registry' )
				.get( name )
				.each( function( wrapper ){ wrapper.handler(); } );
		} catch( e ) {
			console.log( e );
		}
	},
	preremplissageDecisionOrientation: function( modele, index, preremplissages ) {
		var select = modele + index + 'Decisioncov';

		$( select ).observe( 'change', function( event ) {
			var found = false;

			$( preremplissages ).each( function( preremplissage ) {
				if( found === false && $F( select ) === preremplissage.value ) {
					found = true;

					// Type d'orientation
					$( modele + index + 'TypeorientId' ).value = preremplissage.typeorient_id;
					Commission.myTriggerEvent( modele + index + 'TypeorientId', 'change' );

					// Structure référente
					$( modele + index + 'StructurereferenteId' ).value = preremplissage.typeorient_id + '_' + preremplissage.structurereferente_id;
					Commission.myTriggerEvent( modele + index + 'StructurereferenteId', 'change' );

					// Référent
					if( preremplissage.referent_id !== '' ) { // TODO: à vérifier
						$( modele + index + 'ReferentId' ).value = preremplissage.structurereferente_id + '_' + preremplissage.referent_id;
					}
					else {
						$( modele + index + 'ReferentId' ).value = '';
					}
					Commission.myTriggerEvent( modele + index + 'ReferentId', 'change' );
				}
			} );

			if( found === false ) {
				// Type d'orientation
				$( modele + index + 'TypeorientId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'TypeorientId', 'change' );

				// Structure référente
				$( modele + index + 'StructurereferenteId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'StructurereferenteId', 'change' );

				// Référent
				$( modele + index + 'ReferentId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'ReferentId', 'change' );
			}
		} );
	}
};

/* Mise en évidence de champs et d'options de formulaires. @see Evidence (css) */
var Evidence = ( function() {
	'use strict';

	var empty = function( value ) {
			return value === undefined || value === null || value === false;
		},
		getElement = function(selector) {
			var elements = $$(selector);
			if(elements.length === 1) {
				return elements[0];
			}
			return null;
		},
		getQuestion = function (field) {
			if(!empty(field)) {
				return $(field).up('div.input, fieldset');
			}
			return null;
		},
		config = function (params) {
			params['class'] = params['class'] === undefined ? 'evidence' : params['class'];
			params['title'] = params['title'] === undefined ? false : params['title'];

			return params;
		},
		setParams = function (element, params) {
			params = config(params === undefined ? {} : params);

			if( !empty(element) ) {
				if( !empty(params['class']) ) {
					$(element).addClassName(params['class']);
				}
				if( !empty(params['title']) ) {
					$(element).addClassName( 'title' );

					if( $(element).tagName === 'DIV' ) {
						$(element).down('label').title = params['title'];
					}
					else if( $(element).tagName === 'FIELDSET' ) {
						$(element).down('legend').title = params['title'];
					}
					else {
						$(element).title = params['title'];
					}
				}
			}
		};

	return {
		find: function(selector) {
			var element = getElement(selector);
			if( element !== null ) {
				return element;
			}
			element = $$(selector);
			if( element.length === 0 ) {
				console.log( 'Le sélecteur ' + selector + ' ne retourne aucun élément' );
			} else {
				console.log( 'Le sélecteur ' + selector + ' retourne ' + element.length + ' éléments:' );
				console.log( element );
			}

		},
		setQuestionParams: function(selector, params) {
			var question = getQuestion(getElement(selector));

			if( question !== null ) {
				setParams(question, params);
			}
			// TODO: else

		},
		setOptionParams: function(selector, params) {
			var option = getElement(selector);
			if( option !== null ) {

				if( option.type === 'checkbox' ) {
					option = $(option).up('div.input.checkbox, div.checkbox');
				} else if( option.type === 'radio' ) {
					option = getElement('label[for=' + option.id + ']');
				}

				setParams(option, params);
			}
			// TODO: else
		}
	};
} () );

/**
 * Initialisation des tables triables en JavaScript (classe sortable sur la table),
 * pour éviter le lien de tri sur les actions.
 *
 * @param {String} className La classe des tables à trier (sortable par défaut).
 * @returns {undefined}
 */
function initSortableTables( className ) {
	className = ( className === undefined ? 'sortable' : className );

	TableKit.options.rowEvenClass = 'even';
	TableKit.options.rowOddClass = 'odd';
	TableKit.options.descendingClass = 'desc';
	TableKit.options.ascendingClass = 'asc';
	TableKit.options.sortableSelector = ['table.' + className];

	TableKit.Sortable.addSortType(
		new TableKit.Sortable.Type(
			'date-fr',
			{
				'pattern': Webrsa.Regexps.datetime(),
				'normal': function(v) {
					return Webrsa.Date.fromText(v);
				}
			}
		)
	);

	TableKit.Sortable.detectors = $A($w('date-fr date-iso date date-eu date-au time currency datasize number casesensitivetext text'));

	$$( 'table.' + className + ' thead th' ).each( function ( th ) {
		if( $(th).hasClassName( 'actions' ) ) {
			$(th).addClassName( 'nosort' );
		}

		if( $(th).hasClassName( 'date' ) ) {
			$(th).addClassName( 'date-au' );
		}
	} );
}

//------------------------------------------------------------------------------
// Partie "Listes déroulantes liées" pour les projets de villes territoriaux (CG 93)
//------------------------------------------------------------------------------

/**
 * Suppression des optgroups vides d'une list déroulante; s'il n'existe plus qu'un
 * seul optgroup, on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @returns {void}
 */
function cleanSelectOptgroups( selectId ) {
	var optgroups, selected;

	try {
		// On sauvegarde la valeur sélectionnée
		selected = $F(selectId);

		// Suppression des optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			if( 0 === $(optgroup).childElements().length ) {
				$(optgroup).remove();
			}
		} );

		// Si c'est le seul optgroup, on remonte les options d'un niveau
		optgroups = $(selectId).select( 'optgroup' );
		if( 1 === optgroups.length ) {
			$(optgroups[0]).replace( optgroups[0].innerHTML );
		}

		// On remet la valeur sélectionnée
		$(selectId).value = selected;
	} catch( exception ) {
		console.log( exception );
	}
}

/**
 * Permet de limiter une liste déroulante d'options en fonction du préfixe
 * (avec "_" comme séparateur) des valeurs des options ou du préfixe de la
 * valeur sélectionnée.
 *
 * Si le select comporte des optgroups, après limitation des options, les
 * optgroups vides seront supprimés et s'il n'existe plus qu'un seul optgroup,
 * on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @param {String} prefix Le préfixe (sans séparateur)
 * @returns {void}
 */
function limitSelectOptionsByPrefix( selectId, prefix ) {
	try {
		var options = $(selectId).select( 'option' ),
			selected = $F(selectId),
			re = new RegExp( '^(' + prefix + '|' + selected.replace( /_.*$/, '' ) + ')_' ),
			value/*,
			optgroups*/;

		// Restriction de la liste des options
		$(options).each( function( option ) {
			value = $(option).value;
			if( '' != value && null === value.match( re ) ) {
				$(option).remove();
			}
		} );

		cleanSelectOptgroups( selectId );

		/*// Suppression des optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			if( 0 === $(optgroup).childElements().length ) {
				$(optgroup).remove();
			}
		} );

		// Si c'est le seul optgroup, on descend d'un niveau
		optgroups = $(selectId).select( 'optgroup' );
		if( 1 === optgroups.length ) {
			$(optgroups[0]).replace( optgroups[0].innerHTML );
		}

		// On remet la valeur sélectionnée
		$(selectId).value = selected;*/
	} catch( exception ) {
		console.log( exception );
	}
}

//------------------------------------------------------------------------------

/**
 * Suppression des optgroups vides d'une list déroulante; s'il n'existe plus qu'un
 * seul optgroup, on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @returns {void}
 */
function cleanSelectOptgroups2( selectId ) {
	var optgroups, selected, visible;

	try {
		// On sauvegarde la valeur sélectionnée
		selected = $F(selectId);

		// On cache les optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			visible = 0;
			$(optgroup).select( 'option' ).each( function( option ) {
				visible += ( $(option).visible() ? 1 : 0 );
			} );

			if( 0 === visible ) {
				$(optgroup).hide();
			}
		} );
	} catch( exception ) {
		console.log( exception );
	}
}

//------------------------------------------------------------------------------

function onChangeDependantSelect( slaveId, masterId ) {
	try {
		var masterValue = $F(masterId).replace( /^.*_([^_]+)$/, '$1' ),
			slaveValue = $F(slaveId);

		if( false === $(masterId).enabled() ) {
			return;
		}

		// On "reset"
		$(slaveId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).enable();
			$(option).show();
		} );

		if( '' != masterValue ) {
			$(slaveId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && $(option).value.replace( /^([^_]+)_.*$/, '$1' ) !== masterValue ) {
					$(option).disable();
					$(option).hide();
				}
			} );
		} else {
			$(slaveId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).disable();
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(slaveId, slaveValue) ) {
			$(slaveId).value = '';
		}

		cleanSelectOptgroups2(slaveId);

		$(slaveId).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

function dependantSelect( slaveId, masterId ) {
	try {
		if( null !== $(slaveId) ) {
			onChangeDependantSelect( slaveId, masterId );

			$(masterId).observe( 'change', function() {
				onChangeDependantSelect( slaveId, masterId );
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

//------------------------------------------------------------------------------

/**
 * Vérifie si la première option d'un SELECT possédant une valeur donnée est
 * visible.
 *
 * @param {String} selectId
 * @param {String} value
 * @returns {Boolean}
 */
function selectOptionVisible( selectId, value ) {
	var option;

	try {
		option = $(selectId).select( 'option[value="' + value + '"]' );
		return option[0].visible();
	} catch(e) {
		return false;
	}
}

//------------------------------------------------------------------------------

/**
 * Limite la liste des structures référentes en fonction de la valeur sélectionnée
 * dans la liste des PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} structurereferenteId L'id du select contenant les structures référentes
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide ) {
	try {
		var communautesrValue = $F(communautesrId),
			structurereferenteValue = $F(structurereferenteId);

		if( false === $(communautesrId).enabled() ) {
			return;
		}

		// On "reset"
		$(structurereferenteId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).show();
		} );

		if( '' != communautesrValue ) {
			// Suppression des options non présentes dans le projet insertion emploi territorial
			$(structurereferenteId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && false === in_array( $(option).value.replace( /^.*_([^_]+)$/, '$1' ), links[communautesrValue], true ) ) {
					$(option).hide();
				}
			} );

		} else if( true === hide ) {
			$(structurereferenteId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(structurereferenteId, structurereferenteValue) ) {
			$(structurereferenteId).value = '';
		}

		cleanSelectOptgroups2(structurereferenteId);

		// On prévient la liste des structures référentes qu'elle a changé
		$( structurereferenteId ).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

/**
 * Observe le select de projets de villes communautaires afin de limiter la liste
 * des structures référentes à celles se trouvant dans le PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} structurereferenteId L'id du select contenant les structures référentes
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function dependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide ) {
	try {
		if( null !== $(communautesrId) ) {
			onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide );

			$(communautesrId).observe( 'change', function() {
				onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide )
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

/**
 * Limite la liste des référents en fonction de la valeur sélectionnée dans la
 * liste des PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} referentId L'id du select contenant les référents (et dont la valeur est préfixée par l'id de la structure référente)
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide ) {
	try {
		var communautesrValue = $F(communautesrId),
			referentValue = $F(referentId);

		if( false === $(communautesrId).enabled() ) {
			return;
		}

		// On "reset"
		$(referentId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).show();
		} );

		if( '' != communautesrValue ) {
			// Suppression des options non présentes dans le projet insertion emploi territorial
			$(referentId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && false === in_array( $(option).value.replace( /^([^_]+)_.*$/, '$1' ), links[communautesrValue], true ) ) {
					$(option).hide();
				}
			} );

		} else if( true === hide ) {
			$(referentId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(referentId, referentValue) ) {
			$(referentId).value = '';
		}

		cleanSelectOptgroups2(referentId);

		// On prévient la liste des structures référentes qu'elle a changé
		$( referentId ).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

/**
 * Observe le select de projets de villes communautaires afin de limiter la liste
 * des référents à celles se trouvant dans le PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} referentId L'id du select contenant les référents (et dont la valeur est préfixée par l'id de la structure référente)
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function dependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide ) {
	try {
		if( null !== $(communautesrId) ) {
			onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide );

			$(communautesrId).observe( 'change', function() {
				onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide )
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

/**
 * Remplace ou ajoute un paramètre nommé dans une URL "à la CakePHP".
 *
 * @param {String} url
 * @param {String} key
 * @param {*} value
 * @returns {String}
 */
var replaceUrlNamedParam = function(url, key, value) {
	var re = /^([a-z]+:\/\/|\/)([^#]*)(#.*){0,1}$/gi,
		matches = re.exec(url);

	if(null !== matches) {
		if('undefined' === typeof matches[3]) {
			matches[3] = '';
		}

		matches[2] = matches[2].replace( new RegExp( '\/' + key + ':[^\/]+' ), '/' );
		matches[2] = matches[2] + '/' + key + ':' + value;

		url = matches[1] + matches[2].replace( /\/+/g, '/' ) + matches[3];

	}

	return url;
};

/**
 *
 * @see {@url https://stackoverflow.com/a/2593661}
 *
 * @param {String} str
 * @returns {String}
 */
var regExpQuote = function(str) {
    return (str+'').replace(/[.?*+^$[\]\\(){}\/|-]/g, "\\$&");
};


	/**
	* Complète une chaîne jusqu'à une taille donnée.
	*
	* @param {String} value La chaîne d'entrée.
	* @param {Number} length La longueur que doit avoir la chaîne retournée.
	* @param {String|Number} pattern Le caractère devant compléter la chaîne.
	* @returns {@var;pattern|@var;value|String}
	*/
	var pad_left = function(value, length, pattern) {
		var i;

		value = String(value);
		length = Number(length);
		pattern = String(pattern);

		if(1 !== pattern.length) {
			message = "La longueur du paramètre pattern est différente de 1 (" + pattern.length + "): " + pattern;
			throw new Error(message);
		}

		if(value.length < length) {
			for(i=0;i<=length-value.length;i++) {
				value = pattern + value;
			}
		}

		return value;
	};

	/**
	* Permet de manipuler simplement des dates CakePHP dans des selects.
	*
	* @type Object
	*/
	var CakeDateSelects = {
	/**
	* Retourne un objet contenant les attributs year, month et day du champ
	* date désiré.
	*
	* @param {String} id
	* @returns {Object}
	*/
	get: function(id) {
		var result = {}, message;

		try {
			result = {
				year: $F(id + 'Year'),
				month: $F(id + 'Month'),
				day: $F(id + 'Day')
			};
		} catch(e) {
			message = "Impossible de trouver les suffixes Year, Month ou Day pour l'id de champ de date " + id;
			console.error(message);
			result = { year: null, month: null, day: null };
		}

		return result;
	},
	/**
	* Affecte une date donnée au champ date désiré (avec les suffixes Year,
	* Month, Day).
	*
	* @param {String} id
	* @param {Date} target
	* @returns {undefined}
	*/
	set: function(id, target) {
		var year, month, day;

		try {
			year = $(id + 'Year');
			month = $(id + 'Month');
			day = $(id + 'Day');
		} catch(e) {
			message = "Impossible de trouver les suffixes Year, Month ou Day pour l'id de champ de date: " + id;
			console.error(message);
			return;
		}

		try {
			$(year).value = parseInt(target.getFullYear(), 10);
			$(month).value = pad_left(parseInt(target.getMonth(), 10)+1, 2, 0);
			$(day).value = pad_left(parseInt(target.getDate(), 10), 2, 0);
		} catch(e) {
			message = "Le paramètre target n'est pas une date correcte (type " + typeof target + "): " + target;
			console.error(message);
		}
	},
	/**
	* Vérife si les champs ayant le suffixe Year, Month et Day sont vides.
	*
	* @param {String} id
	* @returns {Boolean}
	*/
	empty: function(id) {
		var date = this.get(id);
		return '' === date.year && '' === date.month && '' === date.day;
	}

};

/**
 * Vérifie si la valeur du champ est bien un nombre et si non affiche un message d'erreur
 */
function validateNumber(idChamp){
	let message = document.createElement("div")
	message.classList.add('error-message')
	message.appendChild(document.createTextNode("Veuillez entrer un nombre"))
	if(isNaN( $(idChamp).value)){
	$(idChamp).closest("div").classList.add('error');
	$(idChamp).parentNode.appendChild(message);
	document.querySelector("input[value='Suivant >']").disabled = true;
	} else if ($(idChamp).closest("div").classList.contains('error')) {
		$(idChamp).closest("div").classList.remove('error');
		$(idChamp).parentNode.lastChild.remove();
		if(document.getElementsByClassName('error').length == 0){
			document.querySelector("input[value='Suivant >']").disabled = false;
		}
	}
}
/**
 * Event.simulate(@element, eventName[, options]) -> Element
 * 
 * - @element: element to fire event on
 * - eventName: name of event to fire (only MouseEvents and HTMLEvents interfaces are supported)
 * - options: optional object to fine-tune event properties - pointerX, pointerY, ctrlKey, etc.
 *
 *    $('foo').simulate('click'); // => fires "click" event on an element with id=foo
 *
 **/
(function(){
  
  var eventMatchers = {
    'HTMLEvents': /^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/,
    'MouseEvents': /^(?:click|mouse(?:down|up|over|move|out))$/
  }
  var defaultOptions = {
    pointerX: 0,
    pointerY: 0,
    button: 0,
    ctrlKey: false,
    altKey: false,
    shiftKey: false,
    metaKey: false,
    bubbles: true,
    cancelable: true
  }
  
  Event.simulate = function(element, eventName) {
    var options = Object.extend(defaultOptions, arguments[2] || { });
    var oEvent, eventType = null;
    
    element = $(element);
    
    for (var name in eventMatchers) {
      if (eventMatchers[name].test(eventName)) { eventType = name; break; }
    }

    if (!eventType)
      throw new SyntaxError('Only HTMLEvents and MouseEvents interfaces are supported');

    if (document.createEvent) {
      oEvent = document.createEvent(eventType);
      if (eventType == 'HTMLEvents') {
        oEvent.initEvent(eventName, options.bubbles, options.cancelable);
      }
      else {
        oEvent.initMouseEvent(eventName, options.bubbles, options.cancelable, document.defaultView, 
          options.button, options.pointerX, options.pointerY, options.pointerX, options.pointerY,
          options.ctrlKey, options.altKey, options.shiftKey, options.metaKey, options.button, element);
      }
      element.dispatchEvent(oEvent);
    }
    else {
      options.clientX = options.pointerX;
      options.clientY = options.pointerY;
      oEvent = Object.extend(document.createEventObject(), options);
      element.fireEvent('on' + eventName, oEvent);
    }
    return element;
  }
  
  Element.addMethods({ simulate: Event.simulate });
})()
//
// INFO:
// * si on veut avoir les valeurs exactes des select, on peut voir
//   pour les enlever / remettre avec des classes
// * les textes qu'on met dans la BDD pour les selects ne peuvent
//   pas comprendre ' - ' ... ou alors faire une variable
//
// - http://codylindley.com/Webdev/315/ie-hiding-option-elements-with-css-and-dealing-with-innerhtml
// - http://bytes.com/forum/thread92041.html
// - http://www.javascriptfr.com/codes/GERER-OPTGROUP-LISTE-DEROULANTE_36855.aspx
// - http://www.highdots.com/forums/alt-html/optgroup-optgroup-display-none-style-264456.html
//

//*****************************************************************

function dependantSelectOld( select2Id, select1Id ) {
console.log('vieux');
	var isSelect1 = ( $( select1Id ) !== undefined && $( select1Id ).tagName.toUpperCase() == 'SELECT' );
	var isSelect2 = ( $( select2Id ) !== undefined && $( select2Id ).tagName.toUpperCase() == 'SELECT' );

	if( !isSelect1 || !isSelect2 ) {
		return;
	}

	var selects = new Array();
	var value2 = $F( select2Id );

	// Nettoyage du texte des options
//	$$('#' + select2Id + ' option').each( function ( option ) {
//		var data = $(option).innerHTML;
//		$(option).update( data.replace( new RegExp( '^.* - ', 'gi' ), '' ) );
//	} );

	// Sauvegarde
	if( selects[select2Id] == undefined ) {
		selects[select2Id] = new Array();
		selects[select2Id]['values'] = new Array();
		selects[select2Id]['options'] = new Array();
	}

	$$('#' + select2Id + ' option').each( function ( option ) {
		selects[select2Id]['values'].push( option.value );
		selects[select2Id]['options'].push( option.innerHTML );
	} );

	// INFO: original
//	var pattern = '^[^_]+_';
//	var replacement = '';

	// Là, on est certain de ne prendre que le suffixe
	var pattern = '^(.*_){0,1}([^_]+)$';
	var replacement = '$2';

	// Vidage de la liste
	var select1ValueRegexp = new RegExp( '^' + $F( select1Id ).replace( new RegExp( pattern, 'gi' ), replacement ) + '_', 'gi' );
	$$('#' + select2Id + ' option').each( function ( option ) {
		if( ( $(option).value != '' ) && ( ( $(option).value != '' ) && ( $( option ).value.match( select1ValueRegexp ) == null ) ) )
		$(option).remove();
	} );

	// Onchage event - Partie dynamique
	Event.observe( select1Id, 'change', function( event ) {
		$$('#' + select2Id + ' option').each( function ( option ) {
			$(option).remove();
		} );

		// INFO: pour les select dépendants en cascade
		var select1IdValue = $( select1Id ).value.replace( new RegExp( pattern, 'gi' ), replacement );
		if( select1IdValue !== '' ) {
			var select1IdRegexp = new RegExp( '^' + select1IdValue + '_' );

			for( var i = 0 ; i < selects[select2Id]['values'].length ; i++ ) {
				if( selects[select2Id]['values'][i] == '' || selects[select2Id]['values'][i].match( select1IdRegexp, "g" ) ) {
					$(select2Id).insert( new Element( 'option', { 'value': selects[select2Id]['values'][i] } ).update( selects[select2Id]['options'][i] ) );
				}
			}
		}

		var opt = $$('#' + select2Id + ' option');
		$( opt ).each( function ( option ) {
			if( $(option).value == value2 ) {
				$(option).selected = 'selected';
			}
		} );

		$( select2Id ).simulate( 'change' );
	} );
}

/*
    Masked Input plugin for prototype ported from jQuery 
    Bjarte K. Vebjørnsen <bjartekv at gmail dot com>
        
    Note that the onchange event isn't fired for masked inputs. It won't fire unless event.simulate.js is available.

    Requires: Prototype >= 1.6.1
    Optional: event.simulate.js from http://github.com/kangax/protolicious to trigger native change event.

    Tested on windows IE6, IE7, IE8, Opera 9.6, Chrome 3, FireFox 3, Safari 3
    
    Masked Input plugin for jQuery
    Copyright (c) 2007-2009 Josh Bush (digitalbush.com)
    Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license) 
    Version: 1.2.2 (03/09/2009 22:39:06)
*/

(function() {
    var pasteEventName = (Prototype.Browser.IE ? 'paste' : 'input'),
        iPhone = (window.orientation != undefined);    
            
    if(typeof(Prototype) == "undefined")
        throw "MaskedInput requires Prototype to be loaded.";
                        
    Element.addMethods({
        caret: function(element, begin, end) {
            if (element.length == 0) return;
            if (typeof begin == 'number') {
                end = (typeof end == 'number') ? end : begin;
                if (element.setSelectionRange) {
                    element.focus();
                    element.setSelectionRange(begin, end);
                } else if (element.createTextRange) {
                    var range = element.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', begin);
                    range.select();
                }
            } else {
                if (element.setSelectionRange) {
                    begin = element.selectionStart;
                    end = element.selectionEnd;
                } else if (document.selection && document.selection.createRange) {
                    var range = document.selection.createRange();
                    begin = 0 - range.duplicate().moveStart('character', -100000);
                    end = begin + range.text.length;
                }
                return { begin: begin, end: end };
            }
        }
    });

    MaskedInput = Class.create({
        initialize: function(selector, mask, settings) {  
            this.elements = $$(selector);
            this.mask(mask, settings);	 
        },
        unmask: function() { 
            this.elements.each(function(el) { 
                el.fire("mask:unmask"); 
            }); 
            return this; 
        },
        mask: function (mask, settings) {   
            if (!mask && this.elements.length > 0) {
                var input = $(this.elements[0]);
                var tests = input.retrieve("tests");
                return $A(input.retrieve("buffer")).map(function(c, i) {
                    return tests[i] ? c : null;
                }).join('');
            }
            settings = Object.extend({
                placeholder: "_",
                completed: null
            }, settings || {});	
            
            var defs = MaskedInput.definitions;
            var tests = [];
            var partialPosition = mask.length;
            var firstNonMaskPos = null;
            var len = mask.length;

            $A(mask.split("")).each(function(c, i) {
                if (c == '?') {
                    len--;
                    partialPosition = i;
                } else if (defs[c]) {
                    tests.push(new RegExp(defs[c]));
                    if(firstNonMaskPos==null)
                        firstNonMaskPos =  tests.length - 1;
                } else {
                    tests.push(null);
                }
            });
            
            this.elements.each(function(el) {
            
                var input = $(el);
                
                var buffer = $A(mask.replace(/\?/,'').split("")).map( function(c, i) { return defs[c] ? settings.placeholder : c });

                var ignore = false;  			//Variable for ignoring control keys
                var focusText = input.getValue();
                
                input.store("buffer", buffer).store("tests", tests);

                function seekNext(pos) {
                    while (++pos < len && !tests[pos]);
                    return pos;
                };

                function shiftL(pos) {
                    while (!tests[pos] && --pos >= 0);
                    for (var i = pos; i < len; i++) {
                        if (tests[i]) {
                            buffer[i] = settings.placeholder;
                            var j = seekNext(i);
                            if (j < len && tests[i].test(buffer[j])) {
                                buffer[i] = buffer[j];
                            } else
                                break;
                        }
                    }
                    writeBuffer();
                    input.caret(Math.max(firstNonMaskPos, pos));
                };

                function shiftR(pos) {
                    for (var i = pos, c = settings.placeholder; i < len; i++) {
                        if (tests[i]) {
                            var j = seekNext(i);
                            var t = buffer[i];
                            buffer[i] = c;
                            if (j < len && tests[j].test(t))
                                c = t;
                            else
                                break;
                        }
                    }
                };

                function keydownEvent(e) {
                    var pos = input.caret();
                    var k = e.keyCode;
                    ignore = (k < 16 || (k > 16 && k < 32) || (k > 32 && k < 41));
                    //delete selection before proceeding
                    if ((pos.begin - pos.end) != 0 && (!ignore || k == 8 || k == 46))
                        clearBuffer(pos.begin, pos.end);

                    //backspace, delete, and escape get special treatment
                    if (k == 8 || k == 46 || (iPhone && k == 127)) {//backspace/delete
                        shiftL(pos.begin + (k == 46 ? 0 : -1));
                        e.stop();
                    } else if (k == 27) {//escape
                        input.setValue(focusText);
                        input.caret(0, checkVal());
                        e.stop();
                    }
                };

                function keypressEvent(e) {
                    if (ignore) {
                        ignore = false;
                        //Fixes Mac FF bug on backspace
                        return (e.keyCode == 8) ? false : null;
                    }
                    e = e || window.event;
                    var k = e.charCode || e.keyCode || e.which;
                    var pos = input.caret();
                    if (e.ctrlKey || e.altKey || e.metaKey) {//Ignore
                        return true;
                    } else if ((k >= 32 && k <= 125) || k > 186) {//typeable characters
                        var p = seekNext(pos.begin - 1);
                        if (p < len) {
                            var c = String.fromCharCode(k);
                            if (tests[p].test(c)) {
                                shiftR(p);
                                buffer[p] = c;
                                writeBuffer();
                                var next = seekNext(p);
                                input.caret(next);
                                if (settings.completed && next == len)
                                    settings.completed.call(input);
                            }
                        }
                    }
                    e.stop();
                };
            
                function blurEvent(e) {
                    checkVal();
                    if (input.getValue() != focusText) {
                        // since the native change event doesn't fire we have to fire it ourselves
                        // since Event.fire doesn't support native events we're using Event.simulate if available
                        if (window.Event.simulate) {
                            input.simulate('change');
                        }
                    }
                };
            
                function focusEvent(e) {
                    focusText = input.getValue();
                    var pos = checkVal();
                    writeBuffer();
                        
                    setTimeout(function() {
                        if (pos == mask.length)
                            input.caret(0, pos);
                        else
                            input.caret(pos);
                    }, 0);
                };
            
                function pasteEvent(e) {
                    setTimeout(function() { input.caret(checkVal(true)); }, 0);
                }; 

                function clearBuffer(start, end) {
                    for (var i = start; i < end && i < len; i++) {
                        if (tests[i])
                            buffer[i] = settings.placeholder;
                    }
                    
                };

                function writeBuffer() { return input.setValue(buffer.join('')).getValue(); };

                function checkVal(allow) {
                    //try to place characters where they belong
                    var test = input.getValue();
                    var lastMatch = -1;
                    for (var i = 0, pos = 0; i < len; i++) {
                        if (tests[i]) {
                            buffer[i] = settings.placeholder;
                            while (pos++ < test.length) {
                                var c = test.charAt(pos - 1);
                                if (tests[i].test(c)) {
                                    buffer[i] = c;
                                    lastMatch = i;
                                    break;
                                }
                            }
                            if (pos > test.length)
                                break;
                        } else if (buffer[i] == test[pos] && i!=partialPosition) {
                            pos++;
                            lastMatch = i;
                        } 
                    }
                    
                    if (!allow && lastMatch + 1 < partialPosition) {
                        input.setValue("");
                        clearBuffer(0, len);
                    } else if (allow || lastMatch + 1 >= partialPosition) {
                        writeBuffer();
                        if (!allow) input.setValue(input.getValue().substring(0, lastMatch + 1));
                    }
                    
                    return (partialPosition ? i : firstNonMaskPos);
                };
		
                if (!input.readAttribute("readonly"))
                    input
                    .observe("mask:unmask", function() {
                        input
                            .store("buffer",undefined)
                            .store("tests",undefined)
                            .stopObserving("mask:unmask")
                            .stopObserving("focus", focusEvent)
                            .stopObserving("blur", blurEvent)
                            .stopObserving("keydown", keydownEvent)
                            .stopObserving("keypress", keypressEvent)
                            .stopObserving(pasteEventName, pasteEvent);
                    })
                    .observe("focus", focusEvent)
                    .observe("blur", blurEvent)
                    .observe("keydown", keydownEvent)
                    .observe("keypress", keypressEvent)
                    .observe(pasteEventName, pasteEvent);

                checkVal(); //Perform initial check for existing values
            });
            return this;
        }
    });

    Object.extend(MaskedInput,{    
        definitions: {
            '9': "[0-9]",
            'a': "[A-Za-z]",
            '*': "[A-Za-z0-9]"
        }
    });
})();
/*global console, validationJS, toString, Array, zeroFillDate, giveDefaultValue*/

/*
 * Contien l'équivalent des vérifications de CakePhp et des vérifications php additionnels en Javascript
 *
 * @package app.View.Helper
 * @subpackage FormValidator
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/* @namespace Validation */
var Validation = {
	/**
	 * Vérifi qu'une chaine soit alphaNumérique (accents et charactères étranger autorisé)
	 * @param {String} value
	 * @returns {Boolean}
	 * @function Validation.alphaNumeric
	 */
	alphaNumeric: function( value ) {
		'use strict';
		if ( value === undefined || value === null ){
			return false;
		}
		var test = !Array.isArray(value.match( /[!:;,§\/.?*%\^¨$£=()-+œ<>°@_-`\[\]\\{}#"'~& ]|\s/g )) && value.length > 0;
		return test;
	},

	/**
	 * Alias de la function alphaNumeric()
	 * @param {String} value
	 * @returns {Boolean}
	 */
	alphanumeric: function( value ) {
		'use strict';
		return Validation.alphaNumeric( value );
	},

	/**
	 * Vérifi qu'une chaine soit alphaNumérique (accents et charactères étranger autorisé)
	 * @param {String} value
	 * @returns {Boolean}
	 * @function Validation.alphaNumeric
	 */
	numeric: function( value ) {
		'use strict';
		if ( value === undefined || value === null ){
			return false;
		}
		var test = value.match( /^[0-9]+([.,][0-9]+){0,1}$/ ) !== null;
		return test;
	},

	/**
	 * Vérifi qu'une chaine n'est pas vide (exclu retour à la ligne, tabulation et espaces)
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	notEmpty: function( value ){
		'use strict';
		if ( value === null ){
			return false;
		}

		value = value.replace(/\s/g, '').replace(/ /g, '');
		var test = value.length > 0;
		return test;
	},

	/**
	 * Vérifi qu'une chaine n'est pas vide (exclu retour à la ligne, tabulation et espaces)
	 *
	 * Tests unitaires CakePHP 2.9.7
	 * true === Validation.notBlank('abcdefg')
	 * && true === Validation.notBlank('fasdf ')
	 * && true === Validation.notBlank('fooo' + String.fromCharCode(243) + 'blabla')
	 * && true === Validation.notBlank('abçďĕʑʘπй')
	 * && true === Validation.notBlank('José')
	 * && true === Validation.notBlank('é')
	 * && true === Validation.notBlank('π')
	 * && false === Validation.notBlank("\t ")
	 * && false === Validation.notBlank("");
	 *
	 * Test supplémentaire
	 * true === Validation.notBlank('0');
	 *
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	notBlank: function( value ){
		'use strict';
		if ( value === null ){
			return false;
		}

		value = value.replace(/\s/g, '').replace(/ /g, '');
		var test = value.length > 0;
		return test;
	},

	/**
	 * Vérifi la taille d'une chaine avec valeur min et max (inclu)
	 * @param {String|Number} value
	 * @param {Number} min
	 * @param {Number} max
	 * @returns {Boolean}
	 */
	between: function( value, min, max ){
		'use strict';
		value = String(value).length;
		min = parseInt( min, 10 );
		max = parseInt( max, 10 );

		var test = value >= min && value <= max;
		return test;
	},

	/**
	 * Moteur de inList()
	 * @param {String|Number} value
	 * @param {Array} array
	 * @param {String|Number} sameType
	 * @returns {Boolean}
	 */
	checkIfInList: function( value, array, sameType ){
		'use strict';
		var i;
		if ( typeof toString(value) === 'string' && Array.isArray( array ) ){
			for(i=0; i<array.length; i++){
				array[i] = array[i] === null ? '' : array[i];
				if ( (sameType && value === array[i]) || (!sameType && Validation.similarTo( value, array[i] )) ){
					return true;
				}
			}
		}
		return false;
	},

	/**
	 * Vérifi l'existance de value dans array
	 * @param {String|Number} value
	 * @param {Array} array
	 * @param {String|Number} sameType
	 * @returns {Boolean}
	 */
	inList: function( value, array, sameType ){
		'use strict';
		sameType = giveDefaultValue( sameType, true );
		sameType = ( sameType === 'f' || Validation.similarTo( sameType, 0 ) || Validation.similarTo( sameType, -1 ) || Validation.similarTo( sameType, 'false' ) || sameType === false ) ?
			false : true;

		return Validation.checkIfInList( value, array, sameType );
	},

	/**
	 * Vérifi que la valeur de value est bien entre min et max (inclu ou pas selon le dernier param)
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @param {Boolean} inclusive
	 * @returns {Boolean}
	 */
	inRange: function( value, min, max, inclusive){
		'use strict';
		min = parseFloat( giveDefaultValue( min, -Infinity ) );
		max = parseFloat( giveDefaultValue( max, Infinity ) );
		inclusive = giveDefaultValue( inclusive, true );
		value = parseFloat( value );

		var test = inclusive ? (value >= min && value <= max) : (value > min && value < max);
		return test;
	},

	/**
	 * Alias de la function inRange avec le param inclusive à false
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @returns {Boolean}
	 */
	range: function ( value, min, max ){
		'use strict';
		return Validation.inRange( value, min, max, false );
	},

	/**
	 * Alias de la function inRange avec le param inclusive à true
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @returns {Boolean}
	 */
	inclusiveRange: function ( value, min, max ){
		'use strict';
		return Validation.inRange( value, min, max );
	},

	/**
	 * Vérifi la syntaxe ssn (n° de sécu)
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	ssn: function( value ){
		'use strict';
		value = String(value);
		var test = Array.isArray(value.match( /^(1|2|7|8)[0-9]{2}(0[1-9]|10|11|12|[2-9][0-9])((0[1-9]|[1-8][0-9]|9[0-5]|2A|2B)(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)|(9[7-8][0-9])(0[1-9]|0[1-9]|[1-8][0-9]|90)|99(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990))(00[1-9]|0[1-9][0-9]|[1-9][0-9][0-9]|)(0[1-9]|[1-8][0-9]|9[0-7])$/ ));
		return test;
	},

	/**
	 * Converti, si besoin, une date du format français vers le format anglais (dmy -> ymd)
	 * @param {type} value
	 * @param {type} option
	 * @returns {String}
	 */
	getEnglishDate: function( value, option ){
		'use strict';
		if ( option.toLowerCase() === 'dmy' ){
			// Format de date française selon si l'année est défini sur 4 chiffres ou seulement 2
			value = ( value.indexOf(' ', 7) < 10 || value.indexOf('T') < 10 ) ?
				value.substr(6,2) + value.substr(2,4) + value.substr(0,2) + value.substr(8, value.length -8) :
				value.substr(6,4) + value.substr(2,4) + value.substr(0,2) + value.substr(10, value.length -10)
			;
		}

		return value;
	},

	/**
	 * Vérifi la validitée d'une date
	 * formats acceptés :
	 * YYYY-MM-DD HH:MM:SS -> Format SQL
	 * YY-MM-DD -> Format alternatif, rajoute 1900 si YY > 70 et 2000 si YY < 70 (fonctionne avec tout autre format)
	 * YYYY/MM/DD -> Format standart (fonctionne pour tout autre format)
	 * YYYY.MM.DD -> Format particulier (fonctionne pour tout autre format)
	 * YYYY MM DD -> Format particulier (fonctionne pour tout autre format)
	 * YYYY-MM-DDTHH:MM:SS.000Z-> Format Javascript (avec microsecondes)
	 * DD-MM-YYYY -> Date française
	 * DD-MM-YYYY HH:MM:SS -> DateTime français
	 * HH:MM:SS -> Format heure
	 *
	 * @param {Date} value
	 * @returns {Boolean}
	 */
	date: function( value, option ){
		'use strict';
		option = giveDefaultValue( option, [''] )[0];

		value = Validation.getEnglishDate( value, option );

		// On reformate la date pour faciliter le traitement
		value = Validation.transformIntoDate( value );

		// On converti la date formatté en objet javascript Date et on retransforme en chaine formaté
		var test = new Date( value ).toJSON();
		if ( test === null ){
			return false;
		}

		// On ne garde que les chiffres pour éviter les érreurs dû au multi-bytes
		value = value.replace(/([^0-9]?)/g, '');
		test = test.replace(/([^0-9]?)/g, '');

		// Plus qu'a comparer les dates, si il y a eu un changement ou bien si ça n'a pas fonctionné,
		// c'est que c'est une mauvaise date/syntaxe
		// PS: on vire les microsecondes qui peuvent provoquer des problèmes
		return (test.substr(0,14) === value.substr(0,14));
	},

	/**
	 * Ajoute les parties manquante d'un dateTime (ex: 1/3/15 => 01-03-2015T00:00:00.000Z)
	 * @param {String} value
	 * @returns {String}
	 */
	completeDateTime: function( value ){
		'use strict';
		// Sur la partie date, on s'assure d'avoir des - et non des espaces, des slash ou des points
		// La date doit ressemble à ça pour l'instant : 01-03-2015 11:55:22, on met un T au milieu à la place de l'espace
		value = zeroFillDate( value.substr(0,8).replace(/\.| |\//g, '-') ) + value.substr(8, value.length-8).replace(' ', 'T');

		// Ajoute une date fictive dans le cas d'un Time
		value = value.indexOf('-') > 0 ? value : '01-01-20T' + value;

		// Ajoute un time fictif dans le cas d'un date
		value = value.indexOf('T') > 0 ? value : value + 'T00:00:00';

		// Ajoute les microsecondes si elles n'existent pas
		value = value.indexOf('Z') > 0 ? value : value + '.000Z';

		return value;
	},

	/**
	 * Transforme si besoin, une année de 2 chiffres en 4 chiffres (ex: 15 => 2015)
	 * @param {String} value
	 * @returns {String}
	 */
	yyToyyyy: function( value ){
		'use strict';
		var year = value.substr( 0, value.indexOf('-') );

		// Pour l'année, si seul 2 chiffres sont renseigné, on ajoute 1900 ou 2000 si la valeur est inférieur ou supérieur à 30
		if( year.length === 2 && value.indexOf('T') === 8 ){
			year = year >= 30 ? '19' + year : '20' + year;
		}

		return year;
	},

	/**
	 * Reformate la date au format yyyy-mm-ddThh:mm:ss.mmmZ
	 * @param {String} value
	 * @returns {String}
	 */
	transformIntoDate: function ( value ){
		'use strict';
		var pos;
		if ( Validation.similarTo( value, null ) ) {
			return false;
		}

		value = Validation.completeDateTime( value );

		pos = value.indexOf('-');
		value = Validation.yyToyyyy( value ) + value.substr( pos );

		// Traitements date française JJ-MM-YYYY
		if ( value.indexOf('-') === 2 ){
			value = value.substr(6,4) + value.substr(2,4) + value.substr(0,2) + value.substr(10,value.length -10);
		}

		return value;
	},

	/**
	 * Alias de la function date
	 * @param {String} value
	 * @returns {Boolean}
	 */
	dateTime: function( value ){
		'use strict';
		return Validation.date( value );
	},

	/**
	 * Vérifi la syntaxe d'un numéro de téléphone en france
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	phoneFr: function( value ){
		'use strict';
		value = String(value);
		value = value.length === 9 ? '0' + value : value;
		value = value.replace(/ /g, '').replace(/\./g, '');

		var test = Array.isArray(value.match(/^(((0)[1-9](\s?\d{2}){4})|(1[0-9]{1,3})|(11[0-9]{4})|(3[0-9]{3}))$/));
		return test;
	},

	/**
	 * Vérifi la synthaxe d'une adresse email
	 * @param {type} value
	 * @returns {Boolean}
	 */
	email: function( value ){
		'use strict';
		var test = Array.isArray(value.match(/^[a-z0-9!#$%&\'*+\/=?\^_`{|}~\-]+(?:\.[a-z0-9!#$%&\'*+\/=?\^_`{|}~\-]+)*@(?:[\-_a-z0-9][\-_a-z0-9]*\.)*(?:[a-z0-9][\-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i));
		return test;
	},

	/**
	 * Vérifi qu'un champ possède une valeur de type integer
	 * @param {Number} value
	 * @returns {Boolean}
	 */
	integer: function ( value ){
		'use strict';
		var test = ( !isNaN(value) && value % 1 === 0 && value !== null );
		return  test;
	},

	/**
	 * Vérifi qu'un champ possède une valeur de type boolean
	 * @param {Number|Boolean} value
	 * @returns {Boolean}
	 */
	'boolean': function ( value ){
		'use strict';
		var test = Validation.inList( value, [0, 1, '0', '1', 'true', 'false', true, false] );
		return test;
	},

	/**
	 * Vérifie que le contenu de la liste d'id est vide
	 * @param {Array} value
	 * @returns {Boolean}
	 */
	allEmpty: function ( value ){
		'use strict';
		var i;
		if ( !Array.isArray(value) ){
			return false;
		}

		for (i=0; i<value.length; i++) {
			if ( value[i] !== null && value[i].length > 0 ){
				return false;
			}
		}
		return true;
	},

	/**
	 * Renvoi true si l'input indiqué dans "idInputTest" n'est pas vide ou
	 * si l'input indiqué par "fieldName" possède ou pas ("condition") une valeur
	 * contenu dans "valeurs"
	 * @param {String} value
	 * @param {String} fieldName
	 * @param {Boolean} condition
	 * @param {Array} valeurs
	 * @returns {Boolean}
	 */
	notEmptyIf: function ( value, targetValue, condition, valeurs ){
		'use strict';
		if ( Validation.similarTo( value, null ) || Validation.similarTo( targetValue, null ) || Validation.similarTo( condition, null ) || Validation.similarTo( valeurs, null ) || !Array.isArray( valeurs ) ) {
			return false;
		}

		if ( Validation.inList( targetValue, valeurs, false ) === condition ){
			return Validation.notEmpty( value );
		}
		return true;
	},

	/**
	 * Compare deux dates selon l'operateur de comparaison
	 * @param {String} date1
	 * @param {String} date2
	 * @param {String} operateur
	 * @returns {Boolean}
	 */
	compareDates: function( date1, date2, operateur ){
		'use strict';

		switch ( operateur ) {
			case '<': return date1 < date2;
			case '>': return date1 > date2;
			case '<=': return date1 <= date2;
			case '>=': return date1 >= date2;
			case '==': return Validation.similarTo(date1.toJSON(), date2.toJSON());
			case '!=': return !Validation.similarTo(date1.toJSON(), date2.toJSON());
			case '===': return date1.toJSON() === date2.toJSON();
			case '!==': return date1.toJSON() !== date2.toJSON();
			default: return false;
		}
	},

	/**
	 * Inutile en javascript donc renvoi vers notEmptyIf
	 * @param {String} value
	 * @param {String} fieldName
	 * @param {Boolean} condition
	 * @param {Array} valeurs
	 * @returns {Boolean}
	 */
	notNullIf: function ( idInputTest, idInputMaitre, condition, valeurs ) {
		'use strict';
		return Validation.notEmptyIf( idInputTest, idInputMaitre, condition, valeurs );
	},

	/**
	 * Vérifi que le nombre de char de value ne dépasse pas maxLength
	 * @param {String} value
	 * @param {Numeric} maxLength
	 * @returns {Boolean}
	 */
	maxLength: function ( value, maxLength ){
		'use strict';
		value = value === undefined || value === null ? 0 : value;
		var test = value.length <= maxLength;
		return test;
	},

	/**
	 * Converti les params en String avant de les comparer
	 *
	 * @param {String|Numeric} first
	 * @param {String|Numeric} last
	 * @returns {Boolean}
	 */
	similarTo: function ( first, last ){
		'use strict';
		return String(first) === String(last);
	}
};
/*global console, validationJS, document, validationRules, validationOnsubmit, traductions, Validation, validationOnchange, setTimeout, $, $$, giveDefaultValue, sprintf*/

/*
 * Fait le lien entre FormValidatorHelper et webrsa.validaterules.js
 * Permet la vérification des données d'un formulaire en fonction des règles de validation
 * contenu dans les models. Empèche l'envoi du formulaire et affiche les érreurs si les données
 * ont mal été rempli.
 *
 * @package app.View.Helper
 * @subpackage FormValidator
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
var FormValidator = {
	/**
	 * Liste des variables globales utilisable entre les différentes fonctions
	 * @type json
	 */
	globalVars: {
		rules: [],
		values: {},
		editableList: {},
		errorElements: 'div.input.date, div.input.text, div.input.textarea, div.input.radio, div.input.select',

		// Lié au debug, mettre impérativement à false en production !
		debugMode: false,
		verbose: false,
		ultraVerbose: false
	},

	/**
	 * Permet au php par le biais de cette fonction, de définir des variables globales
	 *
	 * @param {json} varList {validationRules, traductions, validationJS, validationOnchange, validationOnsubmit}
	 * @returns {void}
	 */
	initializeVars: function( varList ) {
		'use strict';
		for (var key in varList ){
			if ( varList.hasOwnProperty( key ) ){
				FormValidator.globalVars[key] = varList[key];
			}
		}
	},

	/**
	 * Permet de récupérer le nom d'un element débarassé de la premiere paire de crochets si besoin est
	 * ex : data[Search][Monmodel][Monchamp] deviens data[Monmodel][Monchamp]
	 *
	 * @param {String} name
	 * @returns {String}
	 */
	getRealName: function( name ) {
		'use strict';
		var regex = /^.+?\[([^\]]+)\]\[([^\]]+)\](\[day\]|\[month\]|\[year\]|\[\]){0,1}$/,
			results = regex.exec( name ),
			returnName
		;

		if ( results === null || results.length !== 4 ) {
			return '';
		}

		returnName = 'data['+results[1]+']['+results[2]+']';

		if ( results[3] !== undefined ) {
			returnName += results[3];
		}

		return returnName;
   },

	/**
	 * On lui donne un nom d'editable et il renvoi le nom du model dont il dépend
	 * getModelName( data[Monmodel][Mon_field] ) = 'Monmodel'
	 *
	 * @param {String} name
	 * @returns {String} ModelName
	 */
	getModelName: function ( name ){
	   'use strict';
	   var crochet1, crochet2;
	   name = FormValidator.getRealName( name );
	   crochet1 = name.indexOf('[');
	   crochet2 = name.indexOf(']');

	   // Il doit exister au moins une paire de crochets
	   if ( crochet1 === -1 || crochet2 === -1 ){
		   return null;
	   }

	   return name.substr(crochet1 +1, crochet2 - crochet1 -1);
	},

	/**
	 * On lui donne un nom d'editable et il renvoi le nom du champ dont il dépend
	 * getFieldName( data[Monmodel][Mon_field] ) = 'Mon_field'
	 *
	 * @param {String} name
	 * @returns {String} ModelName
	 */
	getFieldName: function ( name ){
	   'use strict';
	   var crochet1, crochet2, crochet3, crochet4;
	   name = FormValidator.getRealName( name );
	   crochet1 = name.indexOf('[');
	   crochet2 = name.indexOf(']');
	   crochet3 = name.indexOf('[', crochet1 +1);
	   crochet4 = name.indexOf(']', crochet2 +1);

	   // Il doit exister au moins 2 paires de crochets
	   if ( crochet1 === -1 || crochet2 === -1 || crochet3 === -1 || crochet4 === -1 ){
		   return null;
	   }

	   // Renvoi le contenu de la deuxieme paire de crochets
	   return name.substr(crochet3 +1, crochet4 - crochet3 -1);
	},

	/**
	 * Fonctionne comme getModelName(),
	 * Permet d'obtenir le contenu de la 3e paire de crochets (utile pour les dates)
	 *
	 * @param {String} name
	 * @returns {String}
	 */
	getThirdParam: function ( name ){
		'use strict';
		var crochets, result;
		name = FormValidator.getRealName( name );
		crochets = /^[^\[]*(\[[^\]]*\]){2}\[([^\]]*)\].*$/g,
		result = crochets.exec( name );

		if( result === null || result.length < 3 ) {
			return null;
		}

		return result[2];
	},

	/**
	 * Affiche message en console
	 * Nécéssite l'activation du debug au préalable
	 * Si v est à true, affichera le message seulement si verbose est activé
	 * Si vplus est à true, affichera le message seulement si ultraVerbose est activé
	 * Condition permet d'ajouter une condition suplémentaire
	 *
	 * @param {Mixed} message
	 * @param {Boolean} v (verbose)
	 * @param {Boolean} vplus (ultraVerbose)
	 * @param {Boolean} condition
	 * @returns {void}
	 */
	debug: function ( message, v, vplus, condition ){
		'use strict';
		if ( condition === undefined || condition ){
			v = giveDefaultValue( v, false );
			vplus = giveDefaultValue( vplus, false );

			if ( FormValidator.globalVars.debugMode && ((v &&  FormValidator.globalVars.verbose) || !v) && ((vplus && FormValidator.globalVars.ultraVerbose) || !vplus) ){
				console.log( message );
			}
		}
	},

	/**
	 * Récupère les params d'une rule
	 *
	 * @param {Object} rule
	 * @returns {Array}
	 */
	getParams: function ( rule ){
		'use strict';
		var	i,
			varParams = [];

		for (i=1; i<rule.length; i++){
			varParams.push( rule[i] );
		}

		return varParams;
	},

	/**
	 * Lié à getRules() ajoute une nouvelle règle de validation à un editable
	 *
	 * @param {Object} contain
	 * @returns {Object}
	 */
	addRule: function ( contain ){
		'use strict';
		var		rule = [],
				varAllowEmpty = contain.allowEmpty !== undefined ? contain.allowEmpty : false,
				varParams = [],
				message,
				ruleName;

		// Si le nom de la regle est un String
		if ( typeof contain.rule === 'string' ) {
			ruleName = contain.rule;
		}

		// Si le nom de la regle est stocké dans un array
		else{
			varParams = FormValidator.getParams( contain.rule );

			// Note: contain.rule[0] est l'exacte position du nom de règle dans le cas d'un array
			ruleName = contain.rule[0];
		}

		message = contain.message || (FormValidator.globalVars.traductions[ruleName] || null);
		rule = {name: ruleName, allowEmpty: varAllowEmpty, params: varParams, message: message};

		FormValidator.debug( ('addRule( contain ) - var rule :'), true, true );
		FormValidator.debug( rule, true, true );
		return rule;
	},

	/**
	 * extractRules() constitue le moteur de getRules() (Qui lui, effectue des vérifiactions avant)
	 *
	 * @param {Object} validation
	 * @returns {Object}
	 */
	extractRules: function ( validation ){
		'use strict';
		var key, contain, rules = [];

		// Recherche les regles de validation pour ce champ
		for (key in validation){
			if (validation.hasOwnProperty(key)){
				contain = validation[key];
				FormValidator.debug( ('getRules( name ) - var contain :'), true, true );
				FormValidator.debug( contain, true, true );

				if ( contain.rule !== undefined ){
					rules.push( FormValidator.addRule( contain ) );
				}
				else{
					FormValidator.debug( ('pas de rule trouvé') );
					FormValidator.debug( contain, true );
				}
			}
		}

		FormValidator.debug( '', true, true );
		return rules;
	},

	/**
	 * Renvoi un fieldName dépourvu de _from et de _to pour vérification des between
	 *
	 * @param {String} fieldName
	 * @returns {Boolean}
	 */
	checkIfFromTo: function ( fieldName ){
		'use strict';
		var from, to;

		if( fieldName === null ){
			return false;
		}

		from = fieldName.indexOf('_from');
		to = fieldName.indexOf('_to');

		return from > 0 ? fieldName.substr(0, from) : (to > 0 ? fieldName.substr(0, to) : fieldName);
	},

	/**
	 * Récupère la règle de validation en fonction du nom de l'editable
	 *
	 * @param {String} name
	 * @returns {Object|Boolean}
	 */
	getRules: function ( name ){
		'use strict';
		var modelName, fieldName, rules;
		// Si le json n'existe pas, on renvoi FALSE (on ne peut pas valider sans)
		if ( FormValidator.globalVars.validationRules === undefined ) {
			return false;
		}

		modelName = FormValidator.getModelName( name );
		fieldName = FormValidator.checkIfFromTo ( FormValidator.getFieldName( name ) );

		// Si aucune vérification n'a été trouvé, le champ est correct quoi qu'il arrive
		if ( FormValidator.globalVars.validationRules[modelName] === undefined || FormValidator.globalVars.validationRules[modelName][fieldName] === undefined ){
			return null;
		}

		rules = FormValidator.extractRules( FormValidator.globalVars.validationRules[modelName][fieldName] );

		return rules;
	},

	/**
	 * Concatene les champs date et renvoi leurs valeurs
	 *
	 * @param {Object} listedEditable
	 * @returns {String}
	 */
	extractDate: function ( listedEditable ){
		'use strict';
		var thisDate = {day: '', month: '', year: ''},
			thirdParam, i;

		if ( !listedEditable || listedEditable.length !== 3 ) {
			return false;
		}

		for ( i=0; i<3; i++ ){
			// On récupère day, month ou year et on l'affecte a la variable thisDate
			thirdParam = FormValidator.getThirdParam( listedEditable[i].editable.name );

			switch ( thirdParam ){
				case 'day':
				case 'month':
				case 'year': thisDate[thirdParam] = listedEditable[i].editable.value; break;
				default: return false; // Si ne contien pas day, month ou year, c'est que ce n'est pas une date !
			}

			if ( thisDate[thirdParam] === '' ){
				return '';
			}
		}

		return thisDate;
	},

	/**
	 * Permet d'obtenir les 3 éléments date contenu dans editableList en fonction du name d'origine
	 *
	 * @param {string} name
	 * @param {string} formatedName
	 * @returns {Array|FormValidator.getDateElementsByName.results|Boolean}
	 */
	getDateElementsByName: function ( name, formatedName ) {
		'use strict';
		var list = FormValidator.globalVars.editableList[formatedName],
			results = [],
			regex = /^(.+)\[(?:day|month|year)\]$/g,
			baseName = regex.exec( name ),
			i = 0
		;

		if ( baseName === null || baseName.length !== 2 ) {
			return false;
		}

		for (; i<list.length; i++) {
			switch (list[i].editable.name) {
				case baseName[1]+'[day]':
				case baseName[1]+'[month]':
				case baseName[1]+'[year]':
					results.push(list[i]);
					break;
			}

			if ( results.length === 3 ) {
				return results;
			}
		}

		return false;
	},

	/**
	 * Permet de gérer les elements dates (tordu) de cakephp (les 3 selects)
	 * On lui donne un nom de champ (peut importe si c'est data[Model][field][day] ou data[Model][field][year]...)
	 * Il renvoi la date au format 01-01-2015
	 *
	 * @param {String} name
	 * @returns {String}
	 */
	getDate: function ( name ){
		'use strict';
		var formatedName, thisDate;
		// Converti le name de la forme data[Model][field][day] en Model.field
		formatedName = FormValidator.getModelName( name ) + '.' + FormValidator.getFieldName( name );

		// Il doit y avoir 3 Model.field stocké ( day, month et year )
		if ( FormValidator.globalVars.editableList[formatedName] === undefined || FormValidator.globalVars.editableList[formatedName].length % 3 !== 0 || formatedName === 'null.null' ){
			return false;
		}

		thisDate = FormValidator.extractDate( FormValidator.getDateElementsByName(name, formatedName) );

		if ( !thisDate ){
			return false;
		}

		return thisDate.day + '-' + thisDate.month + '-' + thisDate.year;
	},

	/**
	 * Renvoi la valeur se trouvant après le séparateur ('_' par defaut)
	 * @param {String} value
	 * @param {String} separator
	 * @returns {String}
	 */
	suffix: function ( value, separator ){
		'use strict';
		separator = giveDefaultValue ( separator, '_' );
		var cutPos = value.indexOf(separator) + separator.length;
		return cutPos > 0 ? value.substr(cutPos, value.length) : value;
	},

	/**
	 * Dans le cas d'un fieldName avec un _id, revoi si possible le suffix de cette valeur
	 * @param {HTML} editable
	 * @param {String} value
	 * @returns {String}
	 */
	formatValue: function ( editable, value ){
		'use strict';
		var fieldName = FormValidator.getFieldName( editable.name );
		if ( fieldName.match(/_id/) ){
			value = FormValidator.suffix( value );
		}

		return value.trim();
	},

	/**
	 * Récupère la valeur des boutons radio (renvoi la valeur du bouton selectionné)
	 *
	 * @param {array} targets
	 * @returns {getRadioValue.valeur}
	 */
	getRadioValue: function ( targets ){
		'use strict';
		var i, valeur = '';
		for ( i=0; i<targets.length; i++ ){
			if ( targets[i].checked === true ){
				valeur += valeur.length ? ','+String( targets[i].value ) : String( targets[i].value );
			}
		}

		return valeur;
	},

	/**
	 * Récupère la valeur d'une case à cocher.
	 *
	 * @param {array} targets
	 * @returns {getRadioValue.valeur}
	 */
	getCheckboxValue: function ( targets ){
		'use strict';
		var i, valeur = '', hidden = '';
		for ( i=0; i<targets.length; i++ ){
			if ( 'hidden' === targets[i].type ) {
				hidden = targets[i].value;
			} else if ( true === targets[i].checked ) {
				valeur += valeur.length ? ','+String( targets[i].value ) : String( targets[i].value );
			}
		}

		return 0 < valeur.length ? valeur : hidden;
	},

	/**
	 * Permet de savoir si un editable possède une validation de type téléphone
	 *
	 * @param {HTML} editable
	 * @returns {Boolean}
	 */
	isTelephone: function ( editable ){
		'use strict';
		for (var key in FormValidator.globalVars.rules[editable.index].rules ){
			if ( FormValidator.globalVars.rules[editable.index].rules.hasOwnProperty( key ) && FormValidator.globalVars.rules[editable.index].rules[key].name === 'phoneFr' ){
				return true;
			}
		}
		return false;
	},

	/**
	 * Renvoi la valeur réelle d'un editable
	 * Cherche les élements du même model et même field
	 *
	 * @param {HTML} editable
	 * @returns {String}
	 */
	getValue: function ( editable ){
		'use strict';
		var targets, thisDate, valeur, cas;
		if ( FormValidator.globalVars.rules[editable.index] === undefined ){
			return null;
		}

		targets = $$('[name="' + FormValidator.globalVars.rules[editable.index].name + '"]');

		// Cas Date
		thisDate = FormValidator.getDate( FormValidator.globalVars.rules[editable.index].name );
		cas = thisDate ? 'date' : (	(targets.length === 1) ? 'normal' : targets[1].type );

		switch ( cas ){
			case 'date': valeur = thisDate; break;
			case 'normal': valeur = FormValidator.isTelephone( editable ) ? String( editable.value ).replace(/[\W]/g, '') : String( editable.value ); break;
			case 'radio': valeur = FormValidator.getRadioValue( targets ); break;
			case 'checkbox': valeur = FormValidator.getCheckboxValue( targets ); break;
			default: FormValidator.debug( '/!\\ BUG /!\\ valeur non trouvé dans ' + editable.name ); return null;
		}

		FormValidator.globalVars.values[FormValidator.globalVars.rules[editable.index].name].value = valeur;
		FormValidator.debug( ('Valeur trouvé : ' + valeur), true, true );
		return thisDate || FormValidator.formatValue( editable, valeur );
	},
	empty: function( value ) {
		return value === undefined || value === null || value === false;
	},
	/**
	 * Permet le retrait d'un message d'erreur lié à un editable
	 *
	 * @param {HTML} editable
	 * @returns {Boolean}
	 */
	removeError: function ( editable ){
		'use strict';
		var parentDiv, errorDiv;
		if ( editable === undefined ){
			return false;
		}

		// On remonte vers la div maman pour chercher une erreur à l'interieur
		parentDiv = editable.up(FormValidator.globalVars.errorElements);
		if( false === FormValidator.empty( parentDiv ) ) {
			parentDiv.removeClassName('error');
			errorDiv = parentDiv.select('div.error-message');
		}

		// Si on trouve une erreur affiché, on la retire
		if ( errorDiv !== undefined && errorDiv[0] !== undefined ){
			errorDiv[0].remove();
		}
	},

	/**
	 * Insert les paramètres de la validation dans les %s / %d
	 *
	 * @param {Object} editable
	 * @param {String} message
	 * @returns {String}
	 */
	insertMessageVar: function ( editable, message ){
		'use strict';
		var editableRules = FormValidator.getRules( editable.name ),
			i;

		for(i=0; i<editableRules.length; i++){
			if ( editableRules[i].message === message ){
				switch( editableRules[i].params.length ){
					case 1: message = sprintf( message, editableRules[i].params[0] ); break;
					case 2: message = sprintf( message, editableRules[i].params[0], editableRules[i].params[1] ); break;
					case 3: message = sprintf( message, editableRules[i].params[0], editableRules[i].params[1], editableRules[i].params[2] ); break;
				}
				break;
			}
		}

		return message;
	},

	/**
	 * Affiche l'érreur lié à un editable (ex: Champ obligatoire)
	 *
	 * @param {HTML} editable
	 * @param {String} message
	 * @returns {Boolean}
	 */
	showError: function ( editable, message ){
		'use strict';
		var parentDiv, errorMsg;
		if ( editable === undefined){
			return false;
		}

		setTimeout(function(){
			// Si aucun message n'est indiqué, on affiche Champ obligatoire, sinon le message
			message = message === undefined || message === null ? 'Champ obligatoire' : FormValidator.insertMessageVar( editable, message );

			// On attribu la class error à la div maman de l'editable
			parentDiv = editable.up(FormValidator.globalVars.errorElements);
			if (undefined !== parentDiv) {
				parentDiv.addClassName('error');

				// On verifi si un message d'erreur existe deja
				errorMsg = parentDiv.getElementsByClassName('error-message');

				// On ajoute le message si il n'y en a pas d'autres
				if ( errorMsg.length === 0 ){
					parentDiv.insert('<div class="error-message">' + message + '</div>');
				}
			}
		},20);
	},

	/**
	 * Récupère et formate les params de rule
	 *
	 * @param {HTML} editable
	 * @param {Number} i
	 * @returns {Object}
	 */
	getRulesParams: function ( editable, i ){
		'use strict';
		var params, modelName, targetName, target, name,
		ruleName = FormValidator.globalVars.rules[editable.index].rules[i].name;
		params = Object.create( FormValidator.globalVars.rules[editable.index].rules[i].params );
		name = FormValidator.globalVars.rules[editable.index].name;

		// Validation manquante...
		if ( Validation[ruleName] === undefined ){
			FormValidator.debug( ('Validation manquante : ' + ruleName) );
			FormValidator.debug( params, true );
			return undefined;
		}

		// Cas particulier : notEmptyIf
		if ( ruleName === 'notEmptyIf' || ruleName === 'notNullIf' ){
			modelName = FormValidator.getModelName( name );
			targetName = 'data[' + modelName + '][' + params[0] + ']';
			target = $$('[name="' + targetName + '"]')[0];

			if ( target === undefined ){
				FormValidator.debug( ('Cible du notEmptyIf non trouvé! '+targetName), true );
				FormValidator.debug( ('index = '+editable.index), true );
				FormValidator.debug( (FormValidator.globalVars.rules[editable.index]), true );
				return undefined;
			}
			params[0] = FormValidator.getValue( target );
			FormValidator.debug( ('Target.value = '+params[0]+'; condition = '+params[1]), true );
			FormValidator.debug( (params[2]), true );
		}

		return params;
	},

	/**
	 * Valide ou pas l'editable concerné et affiche l'erreur le cas échéan
	 *
	 * @param {HTML} editable
	 * @param {String} value
	 * @param {Number} i
	 * @param {Object} params
	 * @param {Boolean} isOnchange
	 * @returns {Boolean}
	 */
	isValid: function ( editable, value, i, params, isOnchange ){
		'use strict';
		var message,
			ruleName = FormValidator.globalVars.rules[editable.index].rules[i].name,
			validation = false,
			j,
			val;

		if ( (editable.type.toLowerCase() === 'checkbox' && ruleName === 'date') || editable.type.toLowerCase() === 'hidden' ){
			return true;
		}

		// Cas multiple checkbox
		if (editable.type.toLowerCase() === 'checkbox' && value.indexOf(',') >= 0) {
			val = value.split(',');
			for (j=0; j<val.length; j++) {
				if (FormValidator.isValid(editable, val[j], i, params, isOnchange) === false) {
					return false;
				}
				return true;
			}
		}

		// C'est maintenant qu'on vérifie l'editable
		switch ( params.length ){
			case 0: validation = Validation[ruleName]( value ); break;
			case 1: validation = Validation[ruleName]( value, params[0] ); break;
			case 2: validation = Validation[ruleName]( value, params[0], params[1] ); break;
			case 3: validation = Validation[ruleName]( value, params[0], params[1], params[2] ); break;
		}

		// Si la validation à échoué
		if ( !validation && !(FormValidator.globalVars.rules[editable.index].rules[i].allowEmpty && value.length === 0) ){
			FormValidator.debug( (ruleName+' = false') );

			if ( FormValidator.globalVars.validationOnchange && isOnchange ){
				message = FormValidator.globalVars.rules[editable.index].rules[i].message;
				FormValidator.debug([
					FormValidator.getModelName( editable.name ),
					FormValidator.getFieldName( editable.name ),
					FormValidator.globalVars.validationRules[FormValidator.getModelName( editable.name )][FormValidator.getFieldName( editable.name )],
					message
				], true);
				FormValidator.showError( editable, message );
			}

			return false;
		}

		return true;
	},

	/**
	 * Décide ou pas d'effectuer la verification d'un editable
	 *
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	doValidation: function ( editable, onchange ){
		'use strict';
		var i, params, rule, value;

		rule = FormValidator.globalVars.rules[editable.index].rules;
		value = FormValidator.getValue( editable );

		FormValidator.debug( ('-------------------- validation '+editable.name+' --------------------') );
		FormValidator.debug( ('Valeur = '+value), true );

		if ( rule === null || rule.length <= 0 || rule.allowEmpty ){
			return true;
		}

		// Un editable peut avoir plusieurs regles de validations...
		for (i=0; i<rule.length; i++){
			params = FormValidator.getRulesParams( editable, i );

			if( params !== undefined && !FormValidator.isValid( editable, value, i, params, onchange ) ) {
				return false;
			}
		}

		// Il n'y a pas eu de return false, c'est que l'editable a passer les tests
		FormValidator.debug( FormValidator.globalVars.rules[editable.index].rules[0].name+' = true' );

		return true;
	},

	/**
	 * Moteur de validation (utilise webrsa.validaterules.js)
	 * Vérifi un editable
	 * Renseigner onchange permet ou pas l'affichage du message d'érreur du champ (lié à l'evenement onchange)
	 * Fonctionne avec doValidation()->isValid()
	 *
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	validate: function ( editable, onchange ){
		'use strict';
		if ( editable === undefined || editable === null || editable.index === undefined ||  FormValidator.globalVars.rules[editable.index] === undefined ){
			return true;
		}

		// onchange permet l'affichage des erreurs en true
		// empeche les evenements comme onkeypress de déclancher l'affichage d'erreurs
		onchange = giveDefaultValue( onchange, false );

		// On retire l'éventuel message d'érreur
		if ( FormValidator.globalVars.validationOnchange && onchange ){
			FormValidator.removeError( editable );
		}

		return FormValidator.doValidation( editable, onchange );
	},

	/**
	 * Affiche le message d'erreur sous le menu de navigation (en haut)
	 *
	 * @returns {undefined}
	 */
	showHeaderError: function (){
		'use strict';
		// Affiche le message d'erreur si aucun message n'est trouvé
		$$('#pageContent>p.error, #incrustation_erreur>p.error').each(function( obj ){ obj.remove(); });

		$('incrustation_erreur').innerHTML = '<p class="error"><img src="' + FormValidator.globalVars.baseUrl + 'img/icons/exclamation.png" alt="Erreur">	Erreur lors de l\'enregistrement</p>';
	},

	/**
	 * Vérifi l'intégralité des editables d'un formulaire
	 *
	 * @param {HTML} form
	 * @returns {Boolean}
	 */
	checkAll: function ( form ){
		'use strict';
		// Si variable rules n'existe pas, on envoi le formulaire
		if ( FormValidator.globalVars.rules === undefined || $('noValidation').checked ){
			return true;
		}

		var valid = true;

		// Pour chaque éditables... On vérifi la valeur...
		$$('#' + form.id + ' input, #' + form.id + ' select,' + form.id + ' textarea').each( function( editable ){
			if ( editable.getAttribute('type') !== 'hidden' && !editable.disabled && !FormValidator.validate( $( editable ), true ) ){
				if ( valid ){
					$( editable ).scrollTo();
					window.scrollTo(0, window.pageYOffset);
				}
				FormValidator.debug( ('Validation échoué :') );
				FormValidator.debug( editable );
				valid = false;
			}
		});

		// Si un élément est faux, on n'envoi pas le formulaire
		if ( !valid ){
			// Empeche les boutons submit de se griser
			$$('#' + form.id + ' input[type="submit"], #' + form.id + ' button').each( function( submitButton ){
				setTimeout( function(){ submitButton.removeAttribute('disabled'); }, 100 );
			});
			FormValidator.debug( ('/!\\ Le formulaire n\'a pas été envoyé car il y a un ou plusieurs champs pas/mal rempli.') );

			// Affiche le message d'érreur sous le menu de navigation
			FormValidator.showHeaderError();
			return false;
		}
		FormValidator.debug( ('Validation réussie') );

		// Empeche les submit de se griser en mode debug
		if ( FormValidator.globalVars.debugMode ){
			$$('#' + form.id + ' input[type="submit"], #' + form.id + ' button').each( function( submitButton ){
				setTimeout( function(){ submitButton.removeAttribute('disabled'); }, 100 );
			});
		}

		// En mode debug, empeche l'envoi du formulaire pour afficher en console les informations
		return !FormValidator.globalVars.debugMode;
	},

	/**
	 * Lié à validate()
	 * Attend 10 milisecondes avant de vérifier
	 * Indispensable lors de l'utilisation des evenements onchange et onkeypress
	 * (sinon l'evenement est envoyé avant la modification effective du champ...)
	 *
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	validateWithTimeout: function ( editable, onchange ){
		'use strict';
		setTimeout( FormValidator.validate, 10, editable, onchange );
	},

	/**
	 * Ajoute les evenements onchange, onclick et onsubmit sur les elements concernés.
	 *
	 * @param {HTML} editable
	 * @param {String} type
	 * @returns {void}
	 */
	addEvent: function ( editable, type ){
		'use strict';

		type = type === undefined ? 'editable' : type;

		if ( type === 'editable' ){
			// On lui attribu les evenements onchange et onkeypress qui déclancherons la validation
			Event.observe( editable, 'change', function(){
				// On ne lance la validation que si une différence est trouvé entre l'ancienne et la nouvelle valeur
				FormValidator.getValue( this );
				if ( FormValidator.globalVars.values[this.name].value !== FormValidator.globalVars.values[this.name].oldValue ){
					FormValidator.validateWithTimeout( this, true );
				}
				FormValidator.globalVars.values[this.name].oldValue = String( FormValidator.globalVars.values[this.name].value );
			}); // jshint ignore:line

			Event.observe( editable, 'keypress', function(){
				FormValidator.globalVars.values[this.name].oldValue = FormValidator.globalVars.values[this.name].oldValue === null ? String(  FormValidator.globalVars.values[this.name].value ) : FormValidator.globalVars.values[this.name].oldValue;
				FormValidator.getValue( this );
				// Lance la validation mais sans affichage d'erreur
				FormValidator.validateWithTimeout( this, false );
			});
		}
		else{
			Event.observe(
				editable,
				'submit',
				function( event ) {
					if( $$( 'input[type=hidden][name="data[Cancel]"]' ).length === 0 && FormValidator.checkAll( this ) === false ) {
						Event.stop( event );
					}
				}
			);
		}
	},

	/**
	 * Initialise les objets en rapport avec les editables pour le traitement des vérifications
	 * Leurs ajoute si besoin les evenements via addEvent()
	 *
	 * @param {HTML} editables
	 * @returns {void}
	 */
	initEditables: function ( editables ){
		'use strict';
		var i, name, formatedName, editable;

		// On fait le tour de tout les editables
		for (i=0; i<editables.length; i++){
			name = editables[i].name;
			editables[i].index = i;
			FormValidator.debug( ('window.onload - Element '+name), true, true );

			// Si l'editable n'a pas de nom, on passe à un autre
			if ( name === undefined || name === '' || name === 'Cancel' ){
				continue;
			}

			// Pour chaque editable, on lui attribu des regles de validations (voir getRules() )
			editable = {name: name, id: editables[i].id, rules: FormValidator.getRules(name)};

			// Si ne possède pas une/des règles de validation
			if ( editable.rules === null ){
				continue;
			}

			// Value permet de stocker les valeurs pour vérifier si un champ à été modifié
			FormValidator.globalVars.values[name] = {value: null, oldValue: null};

			// Formate le nom en Model.field
			formatedName = FormValidator.getModelName( name ) + '.' + FormValidator.getFieldName( name );
			FormValidator.debug( ('window.onload - Nom formaté ' + formatedName), true, true );

			// Permet de faire le lien entre les editables ayant le même nom formaté (même model, même champ)
			FormValidator.globalVars.editableList[formatedName] = giveDefaultValue(  FormValidator.globalVars.editableList[formatedName], [] );
			FormValidator.globalVars.editableList[formatedName].push( {editable: editables[i], form: $(editables[i]).up('form')} );

			FormValidator.debug( ('window.onload - var editableList[formatedName] :'), true, true );
			FormValidator.debug( (FormValidator.globalVars.editableList[formatedName]), true, true );

			FormValidator.debug( ('window.onload - var editable :'), true, true );
			FormValidator.debug( editable, true, true );

			// On sauvegarde les regles pour cet editable
			FormValidator.globalVars.rules[i] = editable;

			// On sauvegarde la valeur de l'editable
			FormValidator.globalVars.values[name] = {value: null};
			FormValidator.getValue( editable );

			// On lui attribu les evenements onchange et onkeypress qui déclancherons la validation
			FormValidator.addEvent( editables[i] );

			FormValidator.debug( '--------Fin de l\'attribution de regles pour cet element---------', true, true );
			FormValidator.debug( '', true, true );
			FormValidator.debug( '', true, true );
		}
	},

	/**
	 * Initialise les formulaires pour leur appliquer via addEvent() l'evenement onsubmit
	 *
	 * @param {type} forms
	 * @returns {undefined}
	 */
	initForms: function ( forms ){
		'use strict';
		var key;

		for (key in forms){
			if ( forms.hasOwnProperty(key) ){
				// Empeche l'envoi d'un formulaire non valide si validationOnsubmit est vrais (voir webrsa.inc)
				if ( FormValidator.globalVars.validationOnsubmit !== 1 ){
					break;
				}
				// Envenement onsubmit sur le formulaire (lance une vérification complète)
				FormValidator.addEvent( forms[key], 'form' );
			}
		}
	},

	/**
	 * Attribu les règles de validation pour chaques input|select|textarea (editable)
	 * Surveille égelement quelques evenements :
	 * onsubmit sur les formulaires
	 * onchange et onkeypress sur les editables
	 * Utilise initEditables()|initForms() -> addEvent()
	 *
	 * @returns {Boolean}
	 */
	init: function (){
		'use strict';
		var editables, forms;
		if ( FormValidator.globalVars.validationJS === undefined
				|| FormValidator.globalVars.validationRules === undefined
				|| FormValidator.globalVars.validationOnsubmit === undefined
				|| FormValidator.globalVars.traductions === undefined
				|| Validation === undefined
				|| FormValidator.globalVars.validationOnchange === undefined
				|| giveDefaultValue === undefined ) {
			FormValidator.debug( ('validationJS ou validationRules absent!') );
			return false;
		}

		FormValidator.globalVars.baseUrl = undefined !== FormValidator.globalVars.baseUrl
			? FormValidator.globalVars.baseUrl
			: '/';

		// Editable fait référence à tout ce qui est modifiable par l'utilisateur (input, select et textarea)
		editables = $$('form input, form select, form textarea');

		FormValidator.initEditables( editables );

		FormValidator.debug( '------------------------rules--------------------------', true, true );
		FormValidator.debug( FormValidator.globalVars.rules, true, true );
		FormValidator.debug( '-------------------------end---------------------------', true, true );

		// On diférencie les formulaires
		forms = $$('form');

		FormValidator.initForms( forms );

		if ( $('pageFooter') ){
			$('pageFooter').insert('<input type="checkbox" id="noValidation">');
		}
	}
}

document.observe( "dom:loaded", FormValidator.init );

/*global document, $$, toString, Element*/

/**
 * Polyfill
 * 
 * @source https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Objets_globaux/Object/create#Polyfill
 */
if (typeof Object.create !== 'function') {
	Object.create = (function () {
		'use strict';
		var Temp = function () {
			return;
		};
		return function (prototype) {
			if (arguments.length > 1) {
				throw new Error('Cette prothèse ne supporte pas le second argument');
			}
			if (typeof prototype !== 'object') {
				throw new TypeError('L\'argument doit être un objet');
			}
			Temp.prototype = prototype;
			var result = new Temp();
			Temp.prototype = null;
			return result;
		};
	}());
}


/*************************************************************************
 * Rend les boutons radio décochable si ils portent la class uncheckable *
 *************************************************************************/

/**
 * Décoche un bouton radio renseigné dans radio
 * 
 * @param {HTML} radio
 * @returns {void}
 */
function uncheckable(radio) {
	'use strict';
	radio.onclick = function () {
		$$('input[name="'+radio.name+'"]').each(function (radio) {
			if (radio.checked && radio.state) {
				radio.state = false;
				radio.checked = false;
				
				if (typeof radio.simulate === 'function') {
					radio.simulate('change');
				}
			}
			else if (radio.checked) {
				radio.state = true;
			}
			else{
				radio.state = false;
			}
		});
	};
}


/*************************************************************************
 * Cache les optgroup vide dans un select								 *
 *************************************************************************/

/**
 * Cache les optgroup vide dans un select
 * 
 * @param {HTML} select
 * @returns {boolean}
 */
function removeEmptyOptgroup(select) {
	'use strict';
	var i, j, optgroups = select.select('optgroup'), options, empty;
	
	if (optgroups === null || optgroups.length === 0) {
		return false;
	}
	
	for (i=0; i<optgroups.length; i++) {
		options = optgroups[i].select('option');
		
		// Si il n'y a pas d'option on cache
		if (options === null || options.length === 0) {
			optgroups[i].hide();
			continue;
		}
		
		// Si il y a des options mais qu'elles sont toutes cachés, on cache
		empty = true;
		for (j=0; j<options.length; j++) {
			if (options[j].visible()) {
				empty = false;
				break;
			}
		}
		
		if (empty) {
			optgroups[i].hide();
		} else {
			optgroups[i].show();
		}
	}
	
	return true;
}

/*************************************************************************
 * Ajoute un id au parent de l'élément ciblé							 *
 *************************************************************************/

/**
 * Ajoute un id au parent de l'élément ciblé
 * 
 * @param {DOM} dom
 * @param {integer|string} id
 * @returns {Boolean}
 */
function addParentId(dom, id) {
	'use strict';
	if (dom === undefined || dom === null || dom.up().id !== '') {
		return false;
	}

	dom.up().id = id === undefined ? dom.id + 'Parent' : id;
	return true;
}

/*************************************************************************
 * Organise en deux colonnes											 *
 *************************************************************************/

/**
 * Organise, dans le cas d'un multiple checkbox, en X parties rangés par alpha 
 * de haut en bas et de gauche à droite.
 * Fonctionne également sur tout autre élément avec la même structure :
 * <parent>
 *		<label></label>
 *		<div class="divideInto2Columns">
 *			<label></label>
 *		</div>
 * </parent>
 * @param {HTML} dom
 * @param {integer} nbColumns
 * @returns {Boolean}
 */
function divideIntoColumns(dom, nbColumns) {
	'use strict';
	var parent = dom.up(),
		parentWidth = Element.getWidth(parent), // Pour le calcul de la taille des colonnes
		childs = {}, // Stock les copies de DOM
		childsNames = [], // Utilisé pour trier par alpha
		i = 0,
		divList = [];
	
	// Si deja traité, on retire l'element
	if (parent.divided !== undefined) {
		dom.remove();
		return true;
	}

	parent.divided = true;

	// Si un label seul est présent, il doit avoir une taille de 100% pour eviter le décalage des colonnes
	dom.siblings().each(function (sibling) {
		if (sibling.tagName.toUpperCase() === 'LABEL') {
			sibling.style.width = '100%';
		}
	});

	// Stock les labels et copie les elements
	parent.select('div').each(function (div) {
		var name;
		
		if (div.select('label').length) {
			name = div.select('label').first().innerHTML.replace(/[^A-Za-z]+/g, '');
			childs[name.toUpperCase()] = Element.clone(div, true);
			childsNames.push(name.toUpperCase());
		}
	});

	// Les labels sont trié
	childsNames.sort();

	// On insert les colonnes
	for (; i < nbColumns; i++) {
		divList[i] = new Element('div', {style: 'width:' + Math.floor(parentWidth / nbColumns - 1) + 'px;display:inline-block;vertical-align:top;'});
		parent.insert(divList[i]);
	}

	// On rempli les colonnes dans le bon ordre
	for (i = 0; i < childsNames.length; i++) {
		divList[Math.floor(i / Math.ceil(childsNames.length / nbColumns))].insert(childs[childsNames[i]]);
	}

	// On retire l'ancien element
	dom.remove();

	return true;
}


/*************************************************************************
 * Permet le redimentionnement automatique des textarea					 *
 *************************************************************************/

/**
 * Donne des evenements lors de la modification d'un textarea afin de permetre son redimentionnement
 * @param {DOM} container
 * @returns {void}
 */
function textareaResizeEvents(container) {
	'use strict';
	var area = container.select('textarea').first(),
		span = container.select('span').first();

	// anticipe le redimentionnement pour éviter le clignotement
	area.observe('keydown', function(event) {
		if (event.key.length === 1) {
			span.innerHTML = area.getValue() + event.key;
		}
	});

	area.observe('keyup', function() {
		span.innerHTML = area.getValue();
	});

	area.observe('change', function() {
		span.innerHTML = area.getValue();
	});

	container.addClassName('active');
	span.innerHTML = area.getValue();
}

/**
 * Créer la structure autour du textarea, nécéssaire pour l'auto-redimentionnement
 * Lance textareaResizeEvents() sur la structure ainsi créé
 * @param {DOM} textarea
 * @returns {void}
 */
function makeTextareaAutoExpandable(textarea) {
	'use strict';
	var div, pre, span, newTextarea, visible;
	
	// Poupée russe
	div = new Element('div', {'class': 'autoExpandTextareaContainer'});
	pre = new Element('pre');
	span = new Element('span');
	newTextarea = Element.clone(textarea, true);
	pre.insert(span);
	pre.insert('<br/><br/><br/>');
	div.insert(pre);
	div.insert(newTextarea);
	
	// Le div récupère la taille du textarea si définie à 100%
	visible = textarea.visible();
	if (!visible) {
		textarea.show(); // Permet d'obtenir la vrai valeur css width
	}
	
	if (textarea.getStyle('width') === '100%' || getWidthInPercent(textarea) > 99) {
		div.setStyle({width: '100%'});
	}
	
	// Evite les problèmes liés à des height fixé
	newTextarea.setStyle({height: '100%'});
	
	if (!visible) {
		textarea.hide();
	}
	
	textarea.up().insertBefore(div, textarea);
	textarea.remove();
	
	textareaResizeEvents(div);
}


/*************************************************************************
 * Autres fonctions utiles												 *
 *************************************************************************/

/**
 * Permet d'obtenir la taille en % d'un element
 * @param {DOM} element
 * @returns {float}
 */
function getWidthInPercent(element) {
	var clone = element.clone(),
		percent = 0;
	
	clone.setStyle({width: '100%'});
	element.up().insertBefore(clone, element);
	
	percent = parseFloat(element.getWidth(), 10) / parseFloat(clone.getWidth(), 10) * 100;
	
	clone.remove();
	return percent;
}

/**
 * Si une valeur vaut undefined, lui attribu la defaultValue
 * @param {type} valeur
 * @param {type} defaultValue
 * @returns {unresolved}
 */
function giveDefaultValue(valeur, defaultValue) {
	'use strict';
	return valeur === undefined ? defaultValue : valeur;
}

/**
 * Ajoute les 0 manquant si besoin (ex: 1-2-2015 => 01-02-2015)
 * @param {String} dateString
 * @returns {String}
 */ 
function zeroFillDate(dateString) {
	'use strict';
	return dateString.replace( /^(\d)\-/, '0$1-' ).replace( /\-(\d)\-/, '-0$1-' ).replace( /\-(\d)$/, '-0$1' );
}

/**
 * Vérifi si un array contien une valeur
 * @param {String|Number} value
 * @param {Array} array
 * @returns {Boolean}
 */
function inArray(needle, haystack) {
	'use strict';
	var key;
	if (needle === null || typeof toString(needle) !== 'string' || !Array.isArray( haystack )) {
		return false;
	}
	for (key in haystack) {
		if (haystack.hasOwnProperty(key) && haystack[key] === needle) {
			return true;
		}
	}
	return false;
}

/**
 * Cast d'un array
 * @param {Mixed} values
 * @returns {Array}
 */
function castArray(values) {
	'use strict';
	return typeof values !== 'object' ? [values] : values;
}

/**
 * Permet d'obtenir un identifiant façon cake à partir d'un Model.nomdechamp
 * @param {String} modelField
 * @returns {String}
 */
function fieldId(modelField) {
	'use strict';
	var i, result = '', x, exploded = modelField.split(/[\._]/);
	for (i = 0; i < exploded.length; i++) {
		x = exploded[i];
		result += x.charAt(0).toUpperCase() + x.substring(1);
	}
	return result;
}

/**
 * Equivalent javascript de la fonction php sprintf
 * Fonctionne uniquement pour %s et %d
 * 
 * @param {String} Phrase contenant des %s ou %d
 * @param {String} replace - ajoutez autant d'arguments que nécéssaire
 * @returns {String}
 */
function sprintf() {
	var args = arguments,
		string = args[0],
		i = 1
	;

	return string.replace(/%((%)|s|d)/g, function (m) {
		// m is the matched format, e.g. %s, %d
		var val = null;
		if (m[2]) {
			val = m[2];
		} else {
			val = args[i];
			// A switch statement so that the formatter can be extended. Default is %s
			switch (m) {
				case '%d':
					val = parseFloat(val);
					if (isNaN(val)) {
						val = 0;
					}
					break;
				default:
					break;
			}
			i++;
		}
		return val;
	});
}


/**
 * Rempli un element de type date Cakephp en fonction de la valeur en mois d'un autre élément.
 * 
 * @param {string} id id de l'element qui défini la durée
 * @param {string} target nom de la cible à la façon Cakephp
 * @throws {error} La cible n'a pas été trouvée
 * @returns {Boolean}
 */
function setDateCloture(id, target) {
	'use strict';
	var duree = parseFloat( $F(id), 10 ),
		now = new Date(),
		jour = now.getUTCDate(),
		mois = now.getUTCMonth() +1,
		annee = now.getUTCFullYear(),
		dateButoir,
		exploded = target.split('.'),
		i = 0,
		baseTargetName = 'data',
		targetDay,
		targetMonth,
		targetYear
	;

	if (isNaN(duree*2) || exploded.length < 2) {
		return false;
	}
	
	for (; i<exploded.length; i++) {
		baseTargetName += '['+exploded[i]+']';
	}
	
	targetDay = $$('select[name="'+baseTargetName+'[day]"]').first();
	targetMonth = $$('select[name="'+baseTargetName+'[month]"]').first();
	targetYear = $$('select[name="'+baseTargetName+'[year]"]').first();
	
	if (targetDay === undefined || targetMonth === undefined || targetYear === undefined) {
		throw 'select[name="'+baseTargetName+'"] + ([day] | [month] | [year]) Not Found!';
	}
	
	// Si duree est à virgule, on ajoute 0.x fois 30 jours
	dateButoir = new Date(annee, mois + Math.floor(duree) - 1, ((duree % 1)*30 + jour - 1).toFixed(1));

	targetDay.setValue( dateButoir.getDate() );
	targetMonth.setValue( dateButoir.getMonth() +1 );
	targetYear.setValue( dateButoir.getFullYear() );
	
	targetYear.simulate('change');
}

/**
 * Permet de récupérer un élément sans tenir compte du standard utilisé
 * 
 * @param {string|object} string 'MonElement' ou 'Mon.element' ou $('MonElement)
 * @return {DOM}
 */
function getElementByString(string) {
	'use strict';
	if (string === null) {
		throw "La valeur de l'element est NULL, vous avez probablement tenté de selectionner un element qui n'existe pas";
	}
	
	if (typeof(string) === 'object') {
		// Est déja un élement Prototype
		if (string.tagName !== undefined) {
			return string;
		}
		else {
			throw "getElementByString() do not accept object";
		}
	}
	
	// Format cakephp
	if (string.match(/[\w]+\.[\w]+(\.[\w]+)*/)) {
		return $(fieldId(string));
	}
	
	// Sinon ce doit être déja un id
	return $(string);
}

/**
 * Désactive un element avec ou sans fonction element.disable()
 * 
 * @param {DOM} element à désactiver
 */
function disable(element) {
	if (typeof element.disable === 'function') {
		element.disable();
	} else {
		element.style.pointerEvents = 'none';
	}
}

/**
 * Active un element avec ou sans fonction element.enable()
 * 
 * @param {DOM} element à désactiver
 */
function enable(element) {
	if (typeof element.enable === 'function') {
		element.enable();
	} else {
		element.style.pointerEvents = 'auto';
	}
}

/**
 * Permet sans utiliser eval de comparer la valeur de deux champs en fonction d'un operateur
 * 
 * @param {DOM} value1
 * @param {DOM} value2
 * @param {string} operator accepte : true, =, ==, ===, false, !, !=, !==, <, >, <=, >=
 * @returns {Boolean}
 */
function evalCompare(value1, operator, value2) {
	var result, value1, value2;
	
	switch (operator === undefined ? '=' : operator) {
		case true:
		case '=':
		case '==':
		case '===':
			result = value1 === value2;
			break;
		case false:
		case '!':
		case '!=':
		case '!==':
			result = value1 !== value2;
			break;
		case '<':
			result = parseFloat(value1, 10) < parseFloat(value2, 10);
			break;
		case '>':
			result = parseFloat(value1, 10) > parseFloat(value2, 10);
			break;
		case '<=':
			result = parseFloat(value1, 10) <= parseFloat(value2, 10);
			break;
		case '>=':
			result = parseFloat(value1, 10) >= parseFloat(value2, 10);
			break;
		default:
			throw "operator must be in (true, =, ==, ===, false, !, !=, !==, <, >, <=, >=)";
	}
	
	return result;
}

/**
 * Cache un ou plusieurs élements en fonction d'une ou plusieurs valeurs d'autres elements
 * 
 * Dans values les clefs obligatoire sont : 
 *		- element: 'MonElement' ou 'Mon.element' ou $('MonElement)
 *		- value: Valeur de l'element pour activer/desactiver le disabled
 *		- operateur || operator: Par defaut defini à "=", accepte : true, =, ==, ===, false, !, !=, !==, <, >, <=, >=
 *		
 *	Note :
 *		- Un input checkbox à une valeur soit de null, soit de '1'
 *		- Un input radio à une valeur soit de null, soit du value de l'element
 *		
 * @param {array|string} elements Liste des elements (DOM ou string) sur lesquels appliquer le disable ex: [ $(monElementId), $(monElementId2) ]
 * @param {array|object} values Liste des valeurs à avoir pour appliquer le disable ex: [ {element: $(monElement), value: '1', operator: '!='}, ... ]
 * @param {boolean} hide Si mis à TRUE, cache l'element plutôt que de le griser
 * @param {boolean} oneValueIsValid Si mis à TRUE, une valeur juste parmis la liste suffit à désactiver les elements
 * @param {boolean} debug Fait un console.log des valeurs des elements contenu dans values
 */
function observeDisableElementsOnValues(elements, values, hide, oneValueIsValid, debug) {
	'use strict';
	var i;
	
	elements = elements.constructor !== Array ? [elements] : elements;
	values = values.constructor !== Array ? [values] : values;
	hide = hide === undefined ? false : hide;
	oneValueIsValid = oneValueIsValid === undefined ? true : oneValueIsValid;
	
	disableElementsOnValues(elements, values, hide, oneValueIsValid, debug);
	
	for (i=0; i<values.length; i++) {
		// On s'assure que les clefs sont présente
		if (values[i].element === undefined || values[i].value === undefined) {
			throw "Values must have element and value keys";
		}
		
		getElementByString(values[i].element).observe('change', function() {
			disableElementsOnValues(elements, values, hide, oneValueIsValid, debug);
		});
	}
}

/**
 * Cache un ou plusieurs élements en fonction d'une ou plusieurs valeurs d'autres elements
 * 
 * Dans values les clefs obligatoire sont : 
 *		- element: 'MonElement' ou 'Mon.element' ou $('MonElement)
 *		- value: Valeur de l'element pour activer/desactiver le disabled
 *		- operateur || operator: Par defaut defini à "=", accepte : true, =, ==, ===, false, !, !=, !==, <, >, <=, >=
 *		
 * Note :
 *		- Un input checkbox à une valeur soit de null, soit de '1'
 *		- Un input radio à une valeur soit de null, soit du value de l'element
 * 
 * @param {array|string} elements Liste des elements (DOM ou string) sur lesquels appliquer le disable ex: [ $(monElementId), $(monElementId2) ]
 * @param {array|object} values Liste des valeurs à avoir pour appliquer le disable ex: [ {element: $(monElement), value: '1', operator: '!='}, ... ]
 * @param {boolean} hide Si mis à TRUE, cache l'element plutôt que de le griser
 * @param {boolean} oneValueIsValid Si mis à TRUE, une valeur juste parmis la liste suffit à désactiver les elements
 * @param {boolean} debug Fait un console.log des valeurs des elements contenu dans values
 */
function disableElementsOnValues(elements, values, hide, oneValueIsValid, debug) {
	'use strict';
	var i,
		j,
		element,
		condition = true,
		newCondition,
		valueElement,
		valueName,
		validParents = ['div.input', 'div.checkbox', 'td.action'],
		haveAValidParent
	;
	
	// On commence par formater les variable de façon pour qu'on puisse les traiter pour une seul type (array et boolean)
	elements = elements.constructor !== Array ? [elements] : elements;
	values = values.constructor !== Array ? [values] : values;
	hide = hide === undefined ? false : hide;
	
	oneValueIsValid = oneValueIsValid === undefined ? true : oneValueIsValid;
	
	// On vérifi les valeurs
	for (i=0; i<values.length; i++) {
		// On s'assure que les clefs sont présente
		if (values[i].element === undefined || values[i].value === undefined) {
			throw "Values must have element and value keys";
		}
		
		valueElement = getElementByString(values[i].element);
		valueName = valueElement.id !== null 
			? valueElement.id 
			: (typeof values[i].element === 'string' ? values[i].element : '<object id=null>')
		;
		
		// On s'assure que l'element existe
		if (valueElement === null) {
			throw "Element "+valueName+" is not found!";
		}
		
		// Alias pour operator
		if (values[i].operateur !== undefined) {
			values[i].operator = values[i].operateur;
		}
		
		values[i].operator = values[i].operator === undefined ? '=' : values[i].operator;
		newCondition = evalCompare(valueElement.getValue(), values[i].operator, values[i].value);
		condition = oneValueIsValid && i > 0 ? condition || newCondition : condition && newCondition;
		
		// Pratique pour comprendre pourquoi un element s'active ou se désactive
		if (debug) {
			console.log("----------DEBUG: disableElementsOnValues()----------");
			console.log("Element: '"+valueName+"' targetValue: '"+values[i].operator+" "+values[i].value+"' value: '"+valueElement.getValue()+"' condition: "+(condition ? 'true' : 'false'));
		}
	}
	
	// On applique le disable sur les elements
	for (i=0; i<elements.length; i++) {
		element = getElementByString(elements[i]);
		
		// On s'assure que l'element existe
		if (element === null) {
			throw "Element "+elements[i]+" is not found!";
		}
		
		// Si condition === true alors on desactive/cache l'element
		for (j=0; j<validParents.length; j++) {
			// Les conditions sont rempli, donc on desactive/cache l'element
			if (condition && element.up(validParents[j])) {
				haveAValidParent = true;
				disable(element);
				element.up( validParents[j] ).addClassName( 'disabled' );
				if (hide) {
					element.up( validParents[j] ).hide();
				}
				break;
				
			// Les conditions ne sont pas rempli, on active/montre l'element
			} else if (element.up(validParents[j])) {
				haveAValidParent = true;
				enable(element);
				element.up( validParents[j] ).removeClassName( 'disabled' );
				if (hide) {
					element.up( validParents[j] ).show();
				}
				break;
			}
		}
		
		// Si aucuns parents valide n'a été trouvé, on applique directement sur l'element
		if (!haveAValidParent) {
			if (condition) {
				disable(element);
				element.addClassName( 'disabled' );
				if (hide) {
					element.hide();
				}
			} else {
				enable(element);
				element.removeClassName( 'disabled' );
				if (hide) {
					element.show();
				}
			}
		}
	}
}


if (config === undefined) {
	var config = {debug: 1};
} else {
	config.debug = 1;
}
/**
 * Même principe que le debug php de cakephp
 * Peut être désactivé par disable.debug = true;
 * 
 * @param {mixed} thing
 */
function debug(thing) {
	var stack = new Error().stack.split("\n");

	if (config.debug) {
		console.log('------------------------------------------------');
		console.log('DEBUG '+stack[1]);
		console.log('('+typeof thing+')');
		console.log(thing);
		console.log('------------------------------------------------');
	}
}

/*************************************************************************
 * Execution systématique												 *
 *************************************************************************/

document.observe("dom:loaded", function () {
	'use strict';

	// Rend les boutons radio décochable si ils portent la class uncheckable
	$$('input[type="radio"].uncheckable').each(function (radio) {
		// Ajoute un hidden vide si le bouton n'en possède pas
		var parent = radio.up('fieldset');
		var hidden = parent !== undefined ? parent.select('input[type="hidden"][name="' + radio.name + '"]').first() : undefined;
		if (parent === undefined) {
			parent = radio.up();
		}
		if (hidden === undefined) {
			parent.insert({top: '<input type="hidden" name="' + radio.name + '" value="" />'});
		}

		radio.state = radio.checked;
		uncheckable( radio );
	});

	// Ajoute un visuel sur les input portant la class percent ou euros
	$$('input.percent').each(function (input) {
		input.insert({after: '<div class="input-group-addon">%</div>'});
	});
	$$('input.euros').each(function (input) {
		input.insert({after: '<div class="input-group-addon">€</div>'});
	});

	// Ajoute un id au parent de l'élément ciblé
	$$('.add-parent-id').each(function (dom) {
		addParentId(dom);
	});

	// Divise les elements portant la class divideInto2Columns en deux colonnes
	$$('.divideInto2Columns').each(function (dom) {
		if(!dom.parentElement.classList.contains("divideInto2Columns")) {
			dom.parentElement.classList.add("divideInto2Columns");
		}
		dom.classList.remove("divideInto2Columns");
	});

	// Divise les elements portant la class divideInto3Columns en trois colonnes
	$$('.divideInto3Columns').each(function (dom) {
		if(!dom.parentElement.classList.contains("divideInto3Columns")) {
			dom.parentElement.classList.add("divideInto3Columns");
		}
		dom.classList.remove("divideInto3Columns");
	});
});
/**
 * http://github.com/valums/file-uploader
 *
 * Multiple file upload component with progress-bar, drag-and-drop.
 * © 2010 Andrew Valums ( andrew(at)valums.com )
 *
 * Licensed under GNU GPL 2 or later, see license.txt.
 */

//
// Helper functions
//

var qq = qq || {};

/**
 * Adds all missing properties from second obj to first obj
 */
qq.extend = function(first, second){
    for (var prop in second){
        first[prop] = second[prop];
    }
};

/**
 * Searches for a given element in the array, returns -1 if it is not present.
 * @param {Number} [from] The index at which to begin the search
 */
qq.indexOf = function(arr, elt, from){
    if (arr.indexOf) return arr.indexOf(elt, from);

    from = from || 0;
    var len = arr.length;

    if (from < 0) from += len;

    for (; from < len; from++){
        if (from in arr && arr[from] === elt){
            return from;
        }
    }
    return -1;
};

qq.getUniqueId = (function(){
    var id = 0;
    return function(){ return id++; };
})();

//
// Events

qq.attach = function(element, type, fn){
    if (element.addEventListener){
        element.addEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.attachEvent('on' + type, fn);
    }
};
qq.detach = function(element, type, fn){
    if (element.removeEventListener){
        element.removeEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.detachEvent('on' + type, fn);
    }
};

qq.preventDefault = function(e){
    if (e.preventDefault){
        e.preventDefault();
    } else{
        e.returnValue = false;
    }
};

//
// Node manipulations

/**
 * Insert node a before node b.
 */
qq.insertBefore = function(a, b){
    b.parentNode.insertBefore(a, b);
};
qq.remove = function(element){
    element.parentNode.removeChild(element);
};

qq.contains = function(parent, descendant){
    // compareposition returns false in this case
    if (parent == descendant) return true;

    if (parent.contains){
        return parent.contains(descendant);
    } else {
        return !!(descendant.compareDocumentPosition(parent) & 8);
    }
};

/**
 * Creates and returns element from html string
 * Uses innerHTML to create an element
 */
qq.toElement = (function(){
    var div = document.createElement('div');
    return function(html){
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    };
})();

//
// Node properties and attributes

/**
 * Sets styles for an element.
 * Fixes opacity in IE6-8.
 */
qq.css = function(element, styles){
    if (styles.opacity != null){
        if (typeof element.style.opacity != 'string' && typeof(element.filters) != 'undefined'){
            styles.filter = 'alpha(opacity=' + Math.round(100 * styles.opacity) + ')';
        }
    }
    qq.extend(element.style, styles);
};
qq.hasClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    return re.test(element.className);
};
qq.addClass = function(element, name){
    if (!qq.hasClass(element, name)){
        element.className += ' ' + name;
    }
};
qq.removeClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    element.className = element.className.replace(re, ' ').replace(/^\s+|\s+$/g, "");
};
qq.setText = function(element, text){
    element.innerText = text;
    element.textContent = text;
};

//
// Selecting elements

qq.children = function(element){
    var children = [],
    child = element.firstChild;

    while (child){
        if (child.nodeType == 1){
            children.push(child);
        }
        child = child.nextSibling;
    }

    return children;
};

qq.getByClass = function(element, className){
    if (element.querySelectorAll){
        return element.querySelectorAll('.' + className);
    }

    var result = [];
    var candidates = element.getElementsByTagName("*");
    var len = candidates.length;

    for (var i = 0; i < len; i++){
        if (qq.hasClass(candidates[i], className)){
            result.push(candidates[i]);
        }
    }
    return result;
};

/**
 * obj2url() takes a json-object as argument and generates
 * a querystring. pretty much like jQuery.param()
 *
 * how to use:
 *
 *    `qq.obj2url({a:'b',c:'d'},'http://any.url/upload?otherParam=value');`
 *
 * will result in:
 *
 *    `http://any.url/upload?otherParam=value&a=b&c=d`
 *
 * @param  Object JSON-Object
 * @param  String current querystring-part
 * @return String encoded querystring
 */
qq.obj2url = function(obj, temp, prefixDone){
    var uristrings = [],
        prefix = '&',
        add = function(nextObj, i){
            var nextTemp = temp
                ? (/\[\]$/.test(temp)) // prevent double-encoding
                   ? temp
                   : temp+'['+i+']'
                : i;
            if ((nextTemp != 'undefined') && (i != 'undefined')) {
                uristrings.push(
                    (typeof nextObj === 'object')
                        ? qq.obj2url(nextObj, nextTemp, true)
                        : (Object.prototype.toString.call(nextObj) === '[object Function]')
                            ? encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj())
                            : encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj)
                );
            }
        };

    if (!prefixDone && temp) {
      prefix = (/\?/.test(temp)) ? (/\?$/.test(temp)) ? '' : '&' : '?';
      uristrings.push(temp);
      uristrings.push(qq.obj2url(obj));
    } else if ((Object.prototype.toString.call(obj) === '[object Array]') && (typeof obj != 'undefined') ) {
        // we wont use a for-in-loop on an array (performance)
        for (var i = 0, len = obj.length; i < len; ++i){
            add(obj[i], i);
        }
    } else if ((typeof obj != 'undefined') && (obj !== null) && (typeof obj === "object")){
        // for anything else but a scalar, we will use for-in-loop
        for (var i in obj){
            add(obj[i], i);
        }
    } else {
        uristrings.push(encodeURIComponent(temp) + '=' + encodeURIComponent(obj));
    }

    return uristrings.join(prefix)
                     .replace(/^&/, '')
                     .replace(/%20/g, '+');
};

//
//
// Uploader Classes
//
//

var qq = qq || {};

/**
 * Creates upload button, validates upload, but doesn't create file list or dd.
 */
qq.FileUploaderBasic = function(o){
    this._options = {
        // set to true to see the server response
        debug: false,
        action: '/server/upload',
        params: {},
        button: null,
        multiple: true,
        maxConnections: 3,
        // validation
        allowedExtensions: [],
        sizeLimit: 0,
        minSizeLimit: 0,
        // events
        // return false to cancel submit
        onSubmit: function(id, fileName){},
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, responseJSON){},
        onCancel: function(id, fileName){},
        // messages
        messages: {
            typeError: "{file} has invalid extension. Only {extensions} are allowed.",
            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
            emptyError: "{file} is empty, please select files again without it.",
            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
        },
        showMessage: function(message){
            alert(message);
        }
    };
    qq.extend(this._options, o);

    // number of files being uploaded
    this._filesInProgress = 0;
    this._handler = this._createUploadHandler();

    if (this._options.button){
        this._button = this._createUploadButton(this._options.button);
    }

    this._preventLeaveInProgress();
};

qq.FileUploaderBasic.prototype = {
    setParams: function(params){
        this._options.params = params;
    },
    getInProgress: function(){
        return this._filesInProgress;
    },
    _createUploadButton: function(element){
        var self = this;

        return new qq.UploadButton({
            element: element,
            multiple: this._options.multiple && qq.UploadHandlerXhr.isSupported(),
            onChange: function(input){
                self._onInputChange(input);
            }
        });
    },
    _createUploadHandler: function(){
        var self = this,
            handlerClass;

        if(qq.UploadHandlerXhr.isSupported()){
            handlerClass = 'UploadHandlerXhr';
        } else {
            handlerClass = 'UploadHandlerForm';
        }

        var handler = new qq[handlerClass]({
            debug: this._options.debug,
            action: this._options.action,
            maxConnections: this._options.maxConnections,
            onProgress: function(id, fileName, loaded, total){
                self._onProgress(id, fileName, loaded, total);
                self._options.onProgress(id, fileName, loaded, total);
            },
            onComplete: function(id, fileName, result){
                self._onComplete(id, fileName, result);
                self._options.onComplete(id, fileName, result);
            },
            onCancel: function(id, fileName){
                self._onCancel(id, fileName);
                self._options.onCancel(id, fileName);
            }
        });

        return handler;
    },
    _preventLeaveInProgress: function(){
        var self = this;

        qq.attach(window, 'beforeunload', function(e){
            if (!self._filesInProgress){return;}

            var e = e || window.event;
            // for ie, ff
            e.returnValue = self._options.messages.onLeave;
            // for webkit
            return self._options.messages.onLeave;
        });
    },
    _onSubmit: function(id, fileName){
        this._filesInProgress++;
    },
    _onProgress: function(id, fileName, loaded, total){
    },
    _onComplete: function(id, fileName, result){
        this._filesInProgress--;
        if (result.error){
            this._options.showMessage(result.error);
        }
    },
    _onCancel: function(id, fileName){
        this._filesInProgress--;
    },
    _onInputChange: function(input){
        if (this._handler instanceof qq.UploadHandlerXhr){
            this._uploadFileList(input.files);
        } else {
            if (this._validateFile(input)){
                this._uploadFile(input);
            }
        }
        this._button.reset();
    },
    _uploadFileList: function(files){
        for (var i=0; i<files.length; i++){
            if ( !this._validateFile(files[i])){
                return;
            }
        }

        for (var i=0; i<files.length; i++){
            this._uploadFile(files[i]);
        }
    },
    _uploadFile: function(fileContainer){
        var id = this._handler.add(fileContainer);
        var fileName = this._handler.getName(id);

        if (this._options.onSubmit(id, fileName) !== false){
            this._onSubmit(id, fileName);
            this._handler.upload(id, this._options.params);
        }
    },
    _validateFile: function(file){
        var name, size;

        if (file.value){
            // it is a file input
            // get input value and remove path to normalize
            name = file.value.replace(/.*(\/|\\)/, "");
        } else {
            // fix missing properties in Safari
            name = file.fileName != null ? file.fileName : file.name;
            size = file.fileSize != null ? file.fileSize : file.size;
        }

        if (! this._isAllowedExtension(name)){
            this._error('typeError', name);
            return false;

        } else if (size === 0){
            this._error('emptyError', name);
            return false;

        } else if (size && this._options.sizeLimit && size > this._options.sizeLimit){
            this._error('sizeError', name);
            return false;

        } else if (size && size < this._options.minSizeLimit){
            this._error('minSizeError', name);
            return false;
        }

        return true;
    },
    _error: function(code, fileName){
        var message = this._options.messages[code];
        function r(name, replacement){ message = message.replace(name, replacement); }

        r('{file}', this._formatFileName(fileName));
        r('{extensions}', this._options.allowedExtensions.join(', '));
        r('{sizeLimit}', this._formatSize(this._options.sizeLimit));
        r('{minSizeLimit}', this._formatSize(this._options.minSizeLimit));

        this._options.showMessage(message);
    },
    _formatFileName: function(name){
        if (name.length > 33){
            name = name.slice(0, 19) + '...' + name.slice(-13);
        }
        return name;
    },
    _isAllowedExtension: function(fileName){
        var ext = (-1 !== fileName.indexOf('.')) ? fileName.replace(/.*[.]/, '').toLowerCase() : '';
        var allowed = this._options.allowedExtensions;

        if (!allowed.length){return true;}

        for (var i=0; i<allowed.length; i++){
            if (allowed[i].toLowerCase() == ext){ return true;}
        }

        return false;
    },
    _formatSize: function(bytes){
        var i = -1;
        do {
            bytes = bytes / 1024;
            i++;
        } while (bytes > 99);

        return Math.max(bytes, 0.1).toFixed(1) + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i];
    }
};


/**
 * Class that creates upload widget with drag-and-drop and file list
 * @inherits qq.FileUploaderBasic
 */
qq.FileUploader = function(o){
    // call parent constructor
    qq.FileUploaderBasic.apply(this, arguments);

    // additional options
    qq.extend(this._options, {
        element: null,
        // if set, will be used instead of qq-upload-list in template
        listElement: null,

        template: '<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
                '<div class="qq-upload-button">Upload a file</div>' +
                '<ul class="qq-upload-list"></ul>' +
             '</div>',

        // template for one item in file list
        fileTemplate: '<li>' +
                '<span class="qq-upload-file"></span>' +
                '<span class="qq-upload-spinner"></span>' +
                '<span class="qq-upload-size"></span>' +
                '<a class="qq-upload-cancel" href="#">Cancel</a>' +
                '<span class="qq-upload-failed-text">Failed</span>' +
            '</li>',

        classes: {
            // used to get elements from templates
            button: 'qq-upload-button',
            drop: 'qq-upload-drop-area',
            dropActive: 'qq-upload-drop-area-active',
            list: 'qq-upload-list',

            file: 'qq-upload-file',
            spinner: 'qq-upload-spinner',
            size: 'qq-upload-size',
            cancel: 'qq-upload-cancel',

            // added to list item when upload completes
            // used in css to hide progress spinner
            success: 'qq-upload-success',
            fail: 'qq-upload-fail'
        }
    });
    // overwrite options with user supplied
    qq.extend(this._options, o);

    this._element = this._options.element;
    this._element.innerHTML = this._options.template;
    this._listElement = this._options.listElement || this._find(this._element, 'list');

    this._classes = this._options.classes;

    this._button = this._createUploadButton(this._find(this._element, 'button'));

    this._bindCancelEvent();
    this._setupDragDrop();
};

// inherit from Basic Uploader
qq.extend(qq.FileUploader.prototype, qq.FileUploaderBasic.prototype);

qq.extend(qq.FileUploader.prototype, {
    /**
     * Gets one of the elements listed in this._options.classes
     **/
    _find: function(parent, type){
        var element = qq.getByClass(parent, this._options.classes[type])[0];
        if (!element){
            throw new Error('element not found ' + type);
        }

        return element;
    },
    _setupDragDrop: function(){
        var self = this,
            dropArea = this._find(this._element, 'drop');

        var dz = new qq.UploadDropZone({
            element: dropArea,
            onEnter: function(e){
                qq.addClass(dropArea, self._classes.dropActive);
                e.stopPropagation();
            },
            onLeave: function(e){
                e.stopPropagation();
            },
            onLeaveNotDescendants: function(e){
                qq.removeClass(dropArea, self._classes.dropActive);
            },
            onDrop: function(e){
                dropArea.style.display = 'none';
                qq.removeClass(dropArea, self._classes.dropActive);
                self._uploadFileList(e.dataTransfer.files);
            }
        });

        dropArea.style.display = 'none';

        qq.attach(document, 'dragenter', function(e){
            if (!dz._isValidFileDrag(e)) return;

            dropArea.style.display = 'block';
        });
        qq.attach(document, 'dragleave', function(e){
            if (!dz._isValidFileDrag(e)) return;

            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // only fire when leaving document out
            if ( ! relatedTarget || relatedTarget.nodeName == "HTML"){
                dropArea.style.display = 'none';
            }
        });
    },
    _onSubmit: function(id, fileName){
        qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        this._addToList(id, fileName);
    },
    _onProgress: function(id, fileName, loaded, total){
        qq.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileId(id);
        var size = this._find(item, 'size');
        size.style.display = 'inline';

        var text;
        if (loaded != total){
            text = Math.round(loaded / total * 100) + '% from ' + this._formatSize(total);
        } else {
            text = this._formatSize(total);
        }

        qq.setText(size, text);
    },
    _onComplete: function(id, fileName, result){
        qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

        // mark completed
        var item = this._getItemByFileId(id);
        qq.remove(this._find(item, 'cancel'));
        qq.remove(this._find(item, 'spinner'));

        if (result.success){
            qq.addClass(item, this._classes.success);
        } else {
            qq.addClass(item, this._classes.fail);
        }
    },
    _addToList: function(id, fileName){
        var item = qq.toElement(this._options.fileTemplate);
        item.qqFileId = id;

        var fileElement = this._find(item, 'file');
        qq.setText(fileElement, this._formatFileName(fileName));
        this._find(item, 'size').style.display = 'none';

        this._listElement.appendChild(item);
    },
    _getItemByFileId: function(id){
        var item = this._listElement.firstChild;

        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item){
            if (item.qqFileId == id) return item;
            item = item.nextSibling;
        }
    },
    /**
     * delegate click event for cancel link
     **/
    _bindCancelEvent: function(){
        var self = this,
            list = this._listElement;

        qq.attach(list, 'click', function(e){
            e = e || window.event;
            var target = e.target || e.srcElement;

            if (qq.hasClass(target, self._classes.cancel)){
                qq.preventDefault(e);

                var item = target.parentNode;
                self._handler.cancel(item.qqFileId);
                qq.remove(item);
            }
        });
    }
});

qq.UploadDropZone = function(o){
    this._options = {
        element: null,
        onEnter: function(e){},
        onLeave: function(e){},
        // is not fired when leaving element by hovering descendants
        onLeaveNotDescendants: function(e){},
        onDrop: function(e){}
    };
    qq.extend(this._options, o);

    this._element = this._options.element;

    this._disableDropOutside();
    this._attachEvents();
};

qq.UploadDropZone.prototype = {
    _disableDropOutside: function(e){
        // run only once for all instances
        if (!qq.UploadDropZone.dropOutsideDisabled ){

            qq.attach(document, 'dragover', function(e){
                if (e.dataTransfer){
                    e.dataTransfer.dropEffect = 'none';
                    e.preventDefault();
                }
            });

            qq.UploadDropZone.dropOutsideDisabled = true;
        }
    },
    _attachEvents: function(){
        var self = this;

        qq.attach(self._element, 'dragover', function(e){
            if (!self._isValidFileDrag(e)) return;

            var effect = e.dataTransfer.effectAllowed;
            if (effect == 'move' || effect == 'linkMove'){
                e.dataTransfer.dropEffect = 'move'; // for FF (only move allowed)
            } else {
                e.dataTransfer.dropEffect = 'copy'; // for Chrome
            }

            e.stopPropagation();
            e.preventDefault();
        });

        qq.attach(self._element, 'dragenter', function(e){
            if (!self._isValidFileDrag(e)) return;

            self._options.onEnter(e);
        });

        qq.attach(self._element, 'dragleave', function(e){
            if (!self._isValidFileDrag(e)) return;

            self._options.onLeave(e);

            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // do not fire when moving a mouse over a descendant
            if (qq.contains(this, relatedTarget)) return;

            self._options.onLeaveNotDescendants(e);
        });

        qq.attach(self._element, 'drop', function(e){
            if (!self._isValidFileDrag(e)) return;

            e.preventDefault();
            self._options.onDrop(e);
        });
    },
    _isValidFileDrag: function(e){
        var dt = e.dataTransfer,
            // do not check dt.types.contains in webkit, because it crashes safari 4
            isWebkit = navigator.userAgent.indexOf("AppleWebKit") > -1;

        // dt.effectAllowed is none in Safari 5
        // dt.types.contains check is for firefox
        return dt && dt.effectAllowed != 'none' &&
            (dt.files || (!isWebkit && dt.types.contains && dt.types.contains('Files')));

    }
};

qq.UploadButton = function(o){
    this._options = {
        element: null,
        // if set to true adds multiple attribute to file input
        multiple: false,
        // name attribute of file input
        name: 'file',
        onChange: function(input){},
        hoverClass: 'qq-upload-button-hover',
        focusClass: 'qq-upload-button-focus'
    };

    qq.extend(this._options, o);

    this._element = this._options.element;

    // make button suitable container for input
    qq.css(this._element, {
        position: 'relative',
        overflow: 'hidden',
        // Make sure browse button is in the right side
        // in Internet Explorer
        direction: 'ltr'
    });

    this._input = this._createInput();
};

qq.UploadButton.prototype = {
    /* returns file input element */
    getInput: function(){
        return this._input;
    },
    /* cleans/recreates the file input */
    reset: function(){
        if (this._input.parentNode){
            qq.remove(this._input);
        }

        qq.removeClass(this._element, this._options.focusClass);
        this._input = this._createInput();
    },
    _createInput: function(){
        var input = document.createElement("input");

        if (this._options.multiple){
            input.setAttribute("multiple", "multiple");
        }

        input.setAttribute("type", "file");
        input.setAttribute("name", this._options.name);

        qq.css(input, {
            position: 'absolute',
            // in Opera only 'browse' button
            // is clickable and it is located at
            // the right side of the input
            right: 0,
            top: 0,
            fontFamily: 'Arial',
            // 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
            fontSize: '118px',
            margin: 0,
            padding: 0,
            cursor: 'pointer',
            opacity: 0
        });

        this._element.appendChild(input);

        var self = this;
        qq.attach(input, 'change', function(){
            self._options.onChange(input);
        });

        qq.attach(input, 'mouseover', function(){
            qq.addClass(self._element, self._options.hoverClass);
        });
        qq.attach(input, 'mouseout', function(){
            qq.removeClass(self._element, self._options.hoverClass);
        });
        qq.attach(input, 'focus', function(){
            qq.addClass(self._element, self._options.focusClass);
        });
        qq.attach(input, 'blur', function(){
            qq.removeClass(self._element, self._options.focusClass);
        });

        // IE and Opera, unfortunately have 2 tab stops on file input
        // which is unacceptable in our case, disable keyboard access
        if (window.attachEvent){
            // it is IE or Opera
            input.setAttribute('tabIndex', "-1");
        }

        return input;
    }
};

/**
 * Class for uploading files, uploading itself is handled by child classes
 */
qq.UploadHandlerAbstract = function(o){
    this._options = {
        debug: false,
        action: '/upload.php',
        // maximum number of concurrent uploads
        maxConnections: 999,
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, response){},
        onCancel: function(id, fileName){}
    };
    qq.extend(this._options, o);

    this._queue = [];
    // params for files in queue
    this._params = [];
};
qq.UploadHandlerAbstract.prototype = {
    log: function(str){
        if (this._options.debug && window.console) console.log('[uploader] ' + str);
    },
    /**
     * Adds file or file input to the queue
     * @returns id
     **/
    add: function(file){},
    /**
     * Sends the file identified by id and additional query params to the server
     */
    upload: function(id, params){
        var len = this._queue.push(id);

        var copy = {};
        qq.extend(copy, params);
        this._params[id] = copy;

        // if too many active uploads, wait...
        if (len <= this._options.maxConnections){
            this._upload(id, this._params[id]);
        }
    },
    /**
     * Cancels file upload by id
     */
    cancel: function(id){
        this._cancel(id);
        this._dequeue(id);
    },
    /**
     * Cancells all uploads
     */
    cancelAll: function(){
        for (var i=0; i<this._queue.length; i++){
            this._cancel(this._queue[i]);
        }
        this._queue = [];
    },
    /**
     * Returns name of the file identified by id
     */
    getName: function(id){},
    /**
     * Returns size of the file identified by id
     */
    getSize: function(id){},
    /**
     * Returns id of files being uploaded or
     * waiting for their turn
     */
    getQueue: function(){
        return this._queue;
    },
    /**
     * Actual upload method
     */
    _upload: function(id){},
    /**
     * Actual cancel method
     */
    _cancel: function(id){},
    /**
     * Removes element from queue, starts upload of next
     */
    _dequeue: function(id){
        var i = qq.indexOf(this._queue, id);
        this._queue.splice(i, 1);

        var max = this._options.maxConnections;

        if (this._queue.length >= max){
            var nextId = this._queue[max-1];
            this._upload(nextId, this._params[nextId]);
        }
    }
};

/**
 * Class for uploading files using form and iframe
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerForm = function(o){
    qq.UploadHandlerAbstract.apply(this, arguments);

    this._inputs = {};
};
// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerForm.prototype, qq.UploadHandlerAbstract.prototype);

qq.extend(qq.UploadHandlerForm.prototype, {
    add: function(fileInput){
        fileInput.setAttribute('name', 'qqfile');
        var id = 'qq-upload-handler-iframe' + qq.getUniqueId();

        this._inputs[id] = fileInput;

        // remove file input from DOM
        if (fileInput.parentNode){
            qq.remove(fileInput);
        }

        return id;
    },
    getName: function(id){
        // get input value and remove path to normalize
        return this._inputs[id].value.replace(/.*(\/|\\)/, "");
    },
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));

        delete this._inputs[id];

        var iframe = document.getElementById(id);
        if (iframe){
            // to cancel request set src to something else
            // we use src="javascript:false;" because it doesn't
            // trigger ie6 prompt on https
            iframe.setAttribute('src', 'javascript:false;');

            qq.remove(iframe);
        }
    },
    _upload: function(id, params){
        var input = this._inputs[id];

        if (!input){
            throw new Error('file with passed id was not added, or already uploaded or cancelled');
        }

        var fileName = this.getName(id);

        var iframe = this._createIframe(id);
        var form = this._createForm(iframe, params);
        form.appendChild(input);

        var self = this;
        this._attachLoadEvent(iframe, function(){
            self.log('iframe loaded');

            var response = self._getIframeContentJSON(iframe);

            self._options.onComplete(id, fileName, response);
            self._dequeue(id);

            delete self._inputs[id];
            // timeout added to fix busy state in FF3.6
            setTimeout(function(){
                qq.remove(iframe);
            }, 1);
        });

        form.submit();
        qq.remove(form);

        return id;
    },
    _attachLoadEvent: function(iframe, callback){
        qq.attach(iframe, 'load', function(){
            // when we remove iframe from dom
            // the request stops, but in IE load
            // event fires
            if (!iframe.parentNode){
                return;
            }

            // fixing Opera 10.53
            if (iframe.contentDocument &&
                iframe.contentDocument.body &&
                iframe.contentDocument.body.innerHTML == "false"){
                // In Opera event is fired second time
                // when body.innerHTML changed from false
                // to server response approx. after 1 sec
                // when we upload file with iframe
                return;
            }

            callback();
        });
    },
    /**
     * Returns json object received by iframe from server.
     */
    _getIframeContentJSON: function(iframe){
        // iframe.contentWindow.document - for IE<7
        var doc = iframe.contentDocument ? iframe.contentDocument: iframe.contentWindow.document,
            response;

        this.log("converting iframe's innerHTML to JSON");
        this.log("innerHTML = " + doc.body.innerHTML);

        try {
            response = eval("(" + doc.body.innerHTML + ")");
        } catch(err){
            response = {};
        }

        return response;
    },
    /**
     * Creates iframe with unique name
     */
    _createIframe: function(id){
        // We can't use following code as the name attribute
        // won't be properly registered in IE6, and new window
        // on form submit will open
        // var iframe = document.createElement('iframe');
        // iframe.setAttribute('name', id);

        var iframe = qq.toElement('<iframe src="javascript:false;" name="' + id + '" />');
        // src="javascript:false;" removes ie6 prompt on https

        iframe.setAttribute('id', id);

        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        return iframe;
    },
    /**
     * Creates form, that will be submitted to iframe
     */
    _createForm: function(iframe, params){
        // We can't use the following code in IE6
        // var form = document.createElement('form');
        // form.setAttribute('method', 'post');
        // form.setAttribute('enctype', 'multipart/form-data');
        // Because in this case file won't be attached to request
        var form = qq.toElement('<form method="post" enctype="multipart/form-data"></form>');

        var queryString = qq.obj2url(params, this._options.action);

        form.setAttribute('action', queryString);
        form.setAttribute('target', iframe.name);
        form.style.display = 'none';
        document.body.appendChild(form);

        return form;
    }
});

/**
 * Class for uploading files using xhr
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerXhr = function(o){
    qq.UploadHandlerAbstract.apply(this, arguments);

    this._files = [];
    this._xhrs = [];

    // current loaded size in bytes for each file
    this._loaded = [];
};

// static method
qq.UploadHandlerXhr.isSupported = function(){
    var input = document.createElement('input');
    input.type = 'file';

    return (
        'multiple' in input &&
        typeof File != "undefined" &&
        typeof (new XMLHttpRequest()).upload != "undefined" );
};

// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerXhr.prototype, qq.UploadHandlerAbstract.prototype)

qq.extend(qq.UploadHandlerXhr.prototype, {
    /**
     * Adds file to the queue
     * Returns id to use with upload, cancel
     **/
    add: function(file){
        if (!(file instanceof File)){
            throw new Error('Passed obj in not a File (in qq.UploadHandlerXhr)');
        }

        return this._files.push(file) - 1;
    },
    getName: function(id){
        var file = this._files[id];
        // fix missing name in Safari 4
        return file.fileName != null ? file.fileName : file.name;
    },
    getSize: function(id){
        var file = this._files[id];
        return file.fileSize != null ? file.fileSize : file.size;
    },
    /**
     * Returns uploaded bytes for file identified by id
     */
    getLoaded: function(id){
        return this._loaded[id] || 0;
    },
    /**
     * Sends the file identified by id and additional query params to the server
     * @param {Object} params name-value string pairs
     */
    _upload: function(id, params){
        var file = this._files[id],
            name = this.getName(id),
            size = this.getSize(id);

        this._loaded[id] = 0;

        var xhr = this._xhrs[id] = new XMLHttpRequest();
        var self = this;

        xhr.upload.onprogress = function(e){
            if (e.lengthComputable){
                self._loaded[id] = e.loaded;
                self._options.onProgress(id, name, e.loaded, e.total);
            }
        };

        xhr.onreadystatechange = function(){
            if (xhr.readyState == 4){
                self._onComplete(id, xhr);
            }
        };

        // build query string
        params = params || {};
        params['qqfile'] = name;
        var queryString = qq.obj2url(params, this._options.action);

        xhr.open("POST", queryString, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(name));
        xhr.setRequestHeader("Content-Type", "application/octet-stream");
        xhr.send(file);
    },
    _onComplete: function(id, xhr){
        // the request was aborted/cancelled
        if (!this._files[id]) return;

        var name = this.getName(id);
        var size = this.getSize(id);

        this._options.onProgress(id, name, size, size);

        if (xhr.status == 200){
            this.log("xhr - server response received");
            this.log("responseText = " + xhr.responseText);

            var response;

            try {
                response = eval("(" + xhr.responseText + ")");
            } catch(err){
                response = {};
            }

            this._options.onComplete(id, name, response);

        } else {
            this._options.onComplete(id, name, {});
        }

        this._files[id] = null;
        this._xhrs[id] = null;
        this._dequeue(id);
    },
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));

        this._files[id] = null;

        if (this._xhrs[id]){
            this._xhrs[id].abort();
            this._xhrs[id] = null;
        }
    }
});
/**
 * La classe WebrsaFileUploader étend la classe FileUploader:
 *	- information de statut: Copié ou le message d'erreur
 *	- liens voir et supprimer (@see o.links.add, o.links.delete)
 */
qq.WebrsaFileUploader = function( o ) {
	// call parent constructor
	qq.FileUploader.apply( this, arguments );

	// additional options
	qq.extend( this._options, {
		template: '<div class="qq-uploader">' +
			'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
			'<div class="qq-upload-button">Parcourir</div>' +
			'<ul class="qq-upload-list"></ul>' +
		'</div>',
		// template for one item in file list
		fileTemplate: '<li>' +
			'<span class="qq-upload-file"></span>' +
			'<span class="qq-upload-spinner"></span>' +
			'<span class="qq-upload-size"></span>' +
			'<a class="qq-upload-cancel" href="#">Annuler</a>' +
			'<span class="qq-upload-failed-text">Erreur</span>' +
		'</li>',
		onProgress: function(id, fileName, loaded, total){
			// Il s'agit toujours du dernier élément de la liste, id n'est pas fiable (lorsqu'on supprime un élément)

			// Fix pour l'attribut style écrit en dur
			var spans = $$( '.qq-upload-size' );
			var span = spans[$(spans).length-1];
			$(span).writeAttribute( 'style', '' );

			// Fix pour le nom du fichier tronqué en dur
			var files = $$( '.qq-upload-file' );
			var file = files[$(files).length-1];
			$(file).update( fileName );
		},
		setStatus: function( success, message ) {
			var statuses = $$( '.qq-upload-failed-text' );
			var status = statuses[$(statuses).length-1];

			$(status).update( message );
			$(status).addClassName( 'qq-upload-status-text' );
			if( success ) {
				$(status).addClassName( 'success' );
			}
			else {
				$(status).addClassName( 'error' );
			}
		},
		onComplete: function( id, fileName, responseJSON ) {
			var success = false;
			var message = 'Erreur inattendue';

			// 2°) Traitement du retour de l'appel ajax
			// 2° 1°) Succès
			if( typeof responseJSON.success !== 'undefined' && responseJSON.success === true ) {
				var files = $$( '.qq-upload-file' );
				var file = files[$(files).length-1];

				this.addAjaxUploadedFileLinks( file, fileName );

				message = 'Copié';
				success = true;
			}
			// 2° 2°) Erreur
			else if( typeof responseJSON.error !== 'undefined' ) {
				message = responseJSON.error;
			}

			this.setStatus( success, message );
		},
		showMessage: function( message ) {
			this.setStatus( false, message );
		},
		addAjaxUploadedFileLinks: function( elmt, fileName ) {
			if( typeof fileName === 'undefined' ) {
				fileName = $( elmt ).innerHTML;
			}

			// Lien voir
			var href = o['links']['view'] + '/' + fileName;
			var link = new Element( 'a', { 'href': href, 'class': 'qq-upload-view' } ).update( 'Voir' );
			$( elmt ).up( 'li' ).insert( { bottom: link } );

			// Lien supprimer
			href = o['links']['delete'] + '/' + fileName;
			link = new Element( 'a', { 'href': href, 'class': 'qq-upload-delete' } ).update( 'Supprimer' );
			Event.observe( link, 'click', function(e){
				Event.stop(e);
				new Ajax.Request(
					$(Event.element(e)).getAttribute('href'),
					{
						method: 'post',
						onComplete: function( transport ) {
							try {
								response = eval( '(' + transport.responseText + ')' );
							} catch(err){
								response = {};
							}

							if( response.success && response.success === true ) {
								$( elmt ).up( 'li' ).remove();
							}
							else {
								alert( 'Erreur!' );
							}
						}
					}
				);
			} );
			$( elmt ).up( 'li' ).insert( { bottom: link } );
		}
	} );
	// overwrite options with user supplied
	qq.extend(this._options, o);

	this._element = this._options.element;
	this._element.innerHTML = this._options.template;
	this._listElement = this._options.listElement || this._find(this._element, 'list');

	this._classes = this._options.classes;

	this._button = this._createUploadButton(this._find(this._element, 'button'));

	this._bindCancelEvent();
	this._setupDragDrop();
};

// Inherit from FileUploader
qq.extend( qq.WebrsaFileUploader.prototype, qq.FileUploader.prototype );

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var Cake = Cake !== undefined ? Cake : {};
Cake.Validation = Cake.Validation !== undefined ? Cake.Validation : {};
Cake.Search = Cake.Search !== undefined ? Cake.Search : {};
Cake.Form = Cake.Form !== undefined ? Cake.Form : {};

Cake.Validation.date = function( input ) {
	var date = {'year': null, 'month': null, 'day': null};

	input.select( 'select' ).each( function( select ) {
		['year', 'month', 'day'].each( function(part) {
			var re;

			if( $(select).disabled === false ) {
				re = new RegExp( part );
				if( re.test( $(select).name ) && $(select).value !== '' ) {
					date[part] = $(select).value;
				}
			}
		} );
	} );

	var empty = date['year'] === null && date['month'] === null && date['day'] === null;
	var incomplete = date['year'] === null || date['month'] === null || date['day'] === null;

	return empty || incomplete || isNaN( Date.parse( date['year'] + '-' + date['month'] + '-' + date['day'] ) ) === false;
}

Cake.Form.inputError = function( input, message ) {
	var errors = input.getElementsBySelector( '.error-message' );
	if ( errors.length > 0 ) {
		errors.each( function( error ) {
			$(error).remove();
		} );
	}
	input.removeClassName('error');

	if( message !== undefined ) {
		input.addClassName('error');
		input.insert('<div class="error-message">' + message + '</div>');
	}
};

Cake.Search.validate = function( form ) {
	var success = true;

	$$( '#' + form.id + ' div.input.date' ).each( function( date ) {
		if( Cake.Validation.date( date ) === true ) {
			Cake.Form.inputError( date );
		}
		else {
			Cake.Form.inputError( date, 'Veuillez entrer une date valide.' );
			success = false;
		}
	} );

	return success;
};

Cake.Search.onSubmit = function(event) {
	var form = Event.element(event),
		success = Cake.Search.validate( form );

	if( success === false ) {
		$$( '#' + form.id + ' *[type=submit]', '#' + form.id + ' *[type=reset]' ).each( function( submit ) {
			try{
				submit.enable();
			} catch( err ) {
				submit.disabled = false;
			}
		} );

		Event.stop(event);
		Element.scrollTo(form.getElementsBySelector( '.error-message' )[0].up('div.input'));
	}

	return success;
};
var CakeTabbedPaginator = {
	/**
	 * On ajoute explicitement le paramètre nommé page:1 pour les liens <<,
	 * < et 1
	 * @param {String} paginationLinksSelector Le sélecteur vers les liens de
	 *	pagination de page en page, qui sera complété par ' a'
	 * @returns {undefined}
	 */
	explicitPages: function(paginationLinksSelector) {
		var rel, text;
		try {
			$$(paginationLinksSelector + ' a').each(function(link) {
				rel = $(link).readAttribute( 'rel' );
				text = $(link).innerHTML;
				if('1' === text || 'first' === rel || 'prev' === rel) {
					$(link).href = replaceUrlNamedParam( $(link).href, 'page', '1' );
				}
			});
		} catch( Exception ) {
			console.error( Exception );
		}
	},
	/**
	 *
	 * @param {String} tabSelector
	 * @param {String} paginationSelector
	 * @returns {undefined}
	 */
	initTab: function(tabSelector, paginationSelector) {
		var params = {},
			re = /\/(sort|direction|page)\[([^\]]+)\]:([^\/#]*)/ig,
			matches;

		// Collecte des paramètres nommés ayant des clés dans l'URL
		while( null !== ( matches = re.exec( window.location.href.toString() ) ) ) {
			params[matches[1] + '[' + matches[2] + ']'] = matches[3];
		}

		// Transformation des liens de chacuns des onglets en ajoutant la clé du modèle
		$$(tabSelector).each(function(div) {
			var id = $(div).readAttribute('id');
			['thead a', paginationSelector + ' a'].each(function(selector) {
				$(div).getElementsBySelector(selector).each(function(link) {
					$(link).href = $(link).href.replace( /\/(sort|direction|page):/g, '/$1[' + id + ']:' );
				});
			});
		});

		// Ajout, pour chaque lien, des paramètres nommés ayant des clés dans l'URL s'ils n'existent pas dans le lien
		[tabSelector + ' thead a', tabSelector + ' ' + paginationSelector + ' a'].each(function(selector) {
			$$(selector).each(function(link) {
				for (var key in params) {
					if( params.hasOwnProperty(key) ) {
						re = new RegExp( regExpQuote(key), 'gi' );
						if( null === re.exec( $(link).href ) ) {
							$(link).href = replaceUrlNamedParam( $(link).href, key, params[key] );
						}
					}
				}
			});
		});
	},
	init: function(wrapperId, titleLevel, tabSelector, paginationSelector) {
		wrapperId = 'undefined' === typeof wrapperId ? 'tabbedWrapper' : wrapperId;
		titleLevel = 'undefined' === typeof titleLevel ? 2 : titleLevel;
		tabSelector = 'undefined' === typeof tabSelector ? 'div.tab' : tabSelector;
		paginationSelector = 'undefined' === typeof paginationSelector ? '.pagination' : paginationSelector;

		makeTabbed(wrapperId, titleLevel);

		// Permet de rester sur le bon onglet lorsqu'on trie sur une colonne ou que l'on passe de page en page
		$$(tabSelector).each( function(tab) {
			var id = $(tab).readAttribute('id');
			$(tab).getElementsBySelector( 'thead a', paginationSelector + ' a' ).each( function(link) {
				$(link).writeAttribute( 'href', $(link).readAttribute( 'href' ) + '#' + wrapperId + ',' + id );
			} );
		} );

		this.explicitPages( tabSelector + ' ' + paginationSelector );
		this.initTab(tabSelector, paginationSelector);
	}
};

/*
 * Fabtabulous! Simple tabs using Prototype
 * http://tetlaw.id.au/view/blog/fabtabulous-simple-tabs-using-prototype/
 * Andrew Tetlaw
 * version 1.1 2006-05-06
 * http://creativecommons.org/licenses/by-sa/2.5/
 */
var Fabtabs = Class.create();

Fabtabs.prototype = {
	initialize : function(element) {
		this.element = $(element);
		var options = Object.extend({}, arguments[1] || {});
		this.menu = $A(this.element.getElementsByTagName('a'));
		this.show(this.getInitialTab());
		this.menu.each(this.setupTab.bind(this));
	},
	setupTab : function(elm) {
		Event.observe(elm,'click',this.activate.bindAsEventListener(this),false)
	},
	activate :  function(ev) {
		var elm = Event.findElement(ev, "a");
		Event.stop(ev);
		this.show(elm);
		this.menu.without(elm).each(this.hide.bind(this));
	},
	hide : function(elm) {
		$(elm).removeClassName('active-tab');
		$(this.tabID(elm)).removeClassName('active-tab-body');
	},
	show : function(elm) {
		$(elm).addClassName('active-tab');
		$(this.tabID(elm)).addClassName('active-tab-body');

	},
	tabID : function(elm) {
		return elm.href.match(/#(\w.+)/)[1];
	},
	getInitialTab : function() {
		if(document.location.href.match(/#(\w.+)/)) {
			var loc = RegExp.$1;
			var elm = this.menu.find(function(value) { return value.href.match(/#(\w.+)/)[1] == loc; });
			return elm || this.menu.first();
		} else {
			return this.menu.first();
		}
	}
}

/*
*
* Copyright (c) 2007 Andrew Tetlaw & Millstream Web Software
* http://www.millstream.com.au/view/code/tablekit/
* Version: 1.3b 2008-03-23
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use, copy,
* modify, merge, publish, distribute, sublicense, and/or sell copies
* of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
* BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
* ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
* CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* * 
*/

// Use the TableKit class constructure if you'd prefer to init your tables as JS objects
var TableKit = Class.create();

TableKit.prototype = {
	initialize : function(elm, options) {
		var table = $(elm);
		if(table.tagName !== "TABLE") {
			return;
		}
		TableKit.register(table,Object.extend(TableKit.options,options || {}));
		this.id = table.id;
		var op = TableKit.option('sortable resizable editable', this.id);
		if(op.sortable) {
			TableKit.Sortable.init(table);
		} 
		if(op.resizable) {
			TableKit.Resizable.init(table);
		}
		if(op.editable) {
			TableKit.Editable.init(table);
		}
	},
	sort : function(column, order) {
		TableKit.Sortable.sort(this.id, column, order);
	},
	resizeColumn : function(column, w) {
		TableKit.Resizable.resize(this.id, column, w);
	},
	editCell : function(row, column) {
		TableKit.Editable.editCell(this.id, row, column);
	}
};

Object.extend(TableKit, {
	getBodyRows : function(table) {
		table = $(table);
		var id = table.id;
		if(!TableKit.tables[id].dom.rows) {
			TableKit.tables[id].dom.rows = (table.tHead && table.tHead.rows.length > 0) ? $A(table.tBodies[0].rows) : $A(table.rows).without(table.rows[0]);
		}
		return TableKit.tables[id].dom.rows;
	},
	getHeaderCells : function(table, cell) {
		if(!table) { table = $(cell).up('table'); }
		var id = table.id;
		if(!TableKit.tables[id].dom.head) {
			TableKit.tables[id].dom.head = $A((table.tHead && table.tHead.rows.length > 0) ? table.tHead.rows[table.tHead.rows.length-1].cells : table.rows[0].cells);
		}
		return TableKit.tables[id].dom.head;
	},
	getCellIndex : function(cell) {
		return $A(cell.parentNode.cells).indexOf(cell);
	},
	getRowIndex : function(row) {
		return $A(row.parentNode.rows).indexOf(row);
	},
	getCellText : function(cell, refresh) {
		if(!cell) { return ""; }
		var data = TableKit.getCellData(cell);
		if(refresh || data.refresh || !data.textContent) {
			data.textContent = cell.textContent ? cell.textContent : cell.innerText;
			data.refresh = false;
		}
		return data.textContent;
	},
	getCellData : function(cell) {
	  var t = null;
		if(!cell.id) {
			t = $(cell).up('table');
			cell.id = t.id + "-cell-" + TableKit._getc();
		}
		var tblid = t ? t.id : cell.id.match(/(.*)-cell.*/)[1];
		if(!TableKit.tables[tblid].dom.cells[cell.id]) {
			TableKit.tables[tblid].dom.cells[cell.id] = {textContent : '', htmlContent : '', active : false};
		}
		return TableKit.tables[tblid].dom.cells[cell.id];
	},
	register : function(table, options) {
		if(!table.id) {
			table.id = "tablekit-table-" + TableKit._getc();
		}
		var id = table.id;
		TableKit.tables[id] = TableKit.tables[id] ? 
		                        Object.extend(TableKit.tables[id], options || {}) : 
		                        Object.extend(
		                          {dom : {head:null,rows:null,cells:{}},sortable:false,resizable:false,editable:false},
		                          options || {}
		                        );
	},
	notify : function(eventName, table, event) {
		if(TableKit.tables[table.id] &&  TableKit.tables[table.id].observers && TableKit.tables[table.id].observers[eventName]) {
			TableKit.tables[table.id].observers[eventName](table, event);
		}
		TableKit.options.observers[eventName](table, event)();
	},
	isSortable : function(table) {
		return TableKit.tables[table.id] ? TableKit.tables[table.id].sortable : false;
	},
	isResizable : function(table) {
		return TableKit.tables[table.id] ? TableKit.tables[table.id].resizable : false;
	},
	isEditable : function(table) {
		return TableKit.tables[table.id] ? TableKit.tables[table.id].editable : false;
	},
	setup : function(o) {
		Object.extend(TableKit.options, o || {} );
	},
	option : function(s, id, o1, o2) {
		o1 = o1 || TableKit.options;
		o2 = o2 || (id ? (TableKit.tables[id] ? TableKit.tables[id] : {}) : {});
		var key = id + s;
		if(!TableKit._opcache[key]){
			TableKit._opcache[key] = $A($w(s)).inject([],function(a,v){
				a.push(a[v] = o2[v] || o1[v]);
				return a;
			});
		}
		return TableKit._opcache[key];
	},
	e : function(event) {
		return event || window.event;
	},
	tables : {},
	_opcache : {},
	options : {
		autoLoad : true,
		stripe : true,
		sortable : true,
		resizable : true,
		editable : true,
		rowEvenClass : 'roweven',
		rowOddClass : 'rowodd',
		sortableSelector : ['table.sortable'],
		columnClass : 'sortcol',
		descendingClass : 'sortdesc',
		ascendingClass : 'sortasc',
		defaultSortDirection : 1,
		noSortClass : 'nosort',
		sortFirstAscendingClass : 'sortfirstasc',
		sortFirstDecendingClass : 'sortfirstdesc',
		resizableSelector : ['table.resizable'],
		minWidth : 10,
		showHandle : true,
		resizeOnHandleClass : 'resize-handle-active',
		editableSelector : ['table.editable'],
		formClassName : 'editable-cell-form',
		noEditClass : 'noedit',
		editAjaxURI : '/',
		editAjaxOptions : {},
		observers : {
			'onSortStart' 	: function(){},
			'onSort' 		: function(){},
			'onSortEnd' 	: function(){},
			'onResizeStart' : function(){},
			'onResize' 		: function(){},
			'onResizeEnd' 	: function(){},
			'onEditStart' 	: function(){},
			'onEdit' 		: function(){},
			'onEditEnd' 	: function(){}
		}
	},
	_c : 0,
	_getc : function() {return TableKit._c += 1;},
	unloadTable : function(table){
	  table = $(table);
	  if(!TableKit.tables[table.id]) {return;} //if not an existing registered table return
		var cells = TableKit.getHeaderCells(table);
		var op = TableKit.option('sortable resizable editable noSortClass descendingClass ascendingClass columnClass sortFirstAscendingClass sortFirstDecendingClass', table.id);
		 //unregister all the sorting and resizing events
		cells.each(function(c){
			c = $(c);
			if(op.sortable) {
  			if(!c.hasClassName(op.noSortClass)) {
  				Event.stopObserving(c, 'mousedown', TableKit.Sortable._sort);
  				c.removeClassName(op.columnClass);
  				c.removeClassName(op.sortFirstAscendingClass);
  				c.removeClassName(op.sortFirstDecendingClass);
  				//ensure that if table reloaded current sort is remembered via sort first class name
  				if(c.hasClassName(op.ascendingClass)) {
  				  c.removeClassName(op.ascendingClass);
  				  c.addClassName(op.sortFirstAscendingClass)
  				} else if (c.hasClassName(op.descendingClass)) {
  				  c.removeClassName(op.descendingClass);
  				  c.addClassName(op.sortFirstDecendingClass)
  				}  				
  			}
		  }
		  if(op.resizable) {
  			Event.stopObserving(c, 'mouseover', TableKit.Resizable.initDetect);
  			Event.stopObserving(c, 'mouseout', TableKit.Resizable.killDetect);
		  }
		});
		//unregister the editing events and cancel any open editors
		if(op.editable) {
		  Event.stopObserving(table.tBodies[0], 'click', TableKit.Editable._editCell);
		  for(var c in TableKit.tables[table.id].dom.cells) {
		    if(TableKit.tables[table.id].dom.cells[c].active) {
		      var cell = $(c);
  	      var editor = TableKit.Editable.getCellEditor(cell);
  	      editor.cancel(cell);
		    }
  	  }
		}
		//delete the cache
		TableKit.tables[table.id].dom = {head:null,rows:null,cells:{}}; // TODO: watch this for mem leaks
	},
	reloadTable : function(table){
	  table = $(table);
	  TableKit.unloadTable(table);
	  var op = TableKit.option('sortable resizable editable', table.id);
	  if(op.sortable) {TableKit.Sortable.init(table);}
	  if(op.resizable) {TableKit.Resizable.init(table);}
	  if(op.editable) {TableKit.Editable.init(table);}
	},
	reload : function() {
	  for(var k in TableKit.tables) {
	    TableKit.reloadTable(k);
	  }
	},
	load : function() {
		if(TableKit.options.autoLoad) {
			if(TableKit.options.sortable) {
				$A(TableKit.options.sortableSelector).each(function(s){
					$$(s).each(function(t) {
						TableKit.Sortable.init(t);
					});
				});
			}
			if(TableKit.options.resizable) {
				$A(TableKit.options.resizableSelector).each(function(s){
					$$(s).each(function(t) {
						TableKit.Resizable.init(t);
					});
				});
			}
			if(TableKit.options.editable) {
				$A(TableKit.options.editableSelector).each(function(s){
					$$(s).each(function(t) {
						TableKit.Editable.init(t);
					});
				});
			}
		}
	}
});

TableKit.Rows = {
	stripe : function(table) {
		var rows = TableKit.getBodyRows(table);
		rows.each(function(r,i) {
			TableKit.Rows.addStripeClass(table,r,i);
		});
	},
	addStripeClass : function(t,r,i) {
		t = t || r.up('table');
		var op = TableKit.option('rowEvenClass rowOddClass', t.id);
		var css = ((i+1)%2 === 0 ? op[0] : op[1]);
		// using prototype's assClassName/RemoveClassName was not efficient for large tables, hence:
		var cn = r.className.split(/\s+/);
		var newCn = [];
		for(var x = 0, l = cn.length; x < l; x += 1) {
			if(cn[x] !== op[0] && cn[x] !== op[1]) { newCn.push(cn[x]); }
		}
		newCn.push(css);
		r.className = newCn.join(" ");
	}
};

TableKit.Sortable = {
	init : function(elm, options){
		var table = $(elm);
		if(table.tagName !== "TABLE") {
			return;
		}
		TableKit.register(table,Object.extend(options || {},{sortable:true}));
		var sortFirst;
		var cells = TableKit.getHeaderCells(table);
		var op = TableKit.option('noSortClass columnClass sortFirstAscendingClass sortFirstDecendingClass', table.id);
		cells.each(function(c){
			c = $(c);
			if(!c.hasClassName(op.noSortClass)) {
				Event.observe(c, 'mousedown', TableKit.Sortable._sort);
				c.addClassName(op.columnClass);
				if(c.hasClassName(op.sortFirstAscendingClass) || c.hasClassName(op.sortFirstDecendingClass)) {
					sortFirst = c;
				}
			}
		});

		if(sortFirst) {
			if(sortFirst.hasClassName(op.sortFirstAscendingClass)) {
				TableKit.Sortable.sort(table, sortFirst, 1);
			} else {
				TableKit.Sortable.sort(table, sortFirst, -1);
			}
		} else { // just add row stripe classes
			TableKit.Rows.stripe(table);
		}
	},
	reload : function(table) {
		table = $(table);
		var cells = TableKit.getHeaderCells(table);
		var op = TableKit.option('noSortClass columnClass', table.id);
		cells.each(function(c){
			c = $(c);
			if(!c.hasClassName(op.noSortClass)) {
				Event.stopObserving(c, 'mousedown', TableKit.Sortable._sort);
				c.removeClassName(op.columnClass);
			}
		});
		TableKit.Sortable.init(table);
	},
	_sort : function(e) {
		if(TableKit.Resizable._onHandle) {return;}
		e = TableKit.e(e);
		Event.stop(e);
		var cell = Event.element(e);
		while(!(cell.tagName && cell.tagName.match(/td|th/gi))) {
			cell = cell.parentNode;
		}
		TableKit.Sortable.sort(null, cell);
	},
	sort : function(table, index, order) {
		var cell;
		if(typeof index === 'number') {
			if(!table || (table.tagName && table.tagName !== "TABLE")) {
				return;
			}
			table = $(table);
			index = Math.min(table.rows[0].cells.length, index);
			index = Math.max(1, index);
			index -= 1;
			cell = (table.tHead && table.tHead.rows.length > 0) ? $(table.tHead.rows[table.tHead.rows.length-1].cells[index]) : $(table.rows[0].cells[index]);
		} else {
			cell = $(index);
			table = table ? $(table) : cell.up('table');
			index = TableKit.getCellIndex(cell);
		}
		var op = TableKit.option('noSortClass descendingClass ascendingClass defaultSortDirection', table.id);
		
		if(cell.hasClassName(op.noSortClass)) {return;}	
		//TableKit.notify('onSortStart', table);
		order = order ? order : op.defaultSortDirection;
		var rows = TableKit.getBodyRows(table);

		if(cell.hasClassName(op.ascendingClass) || cell.hasClassName(op.descendingClass)) {
			rows.reverse(); // if it was already sorted we just need to reverse it.
			order = cell.hasClassName(op.descendingClass) ? 1 : -1;
		} else {
			var datatype = TableKit.Sortable.getDataType(cell,index,table);
			var tkst = TableKit.Sortable.types;
			rows.sort(function(a,b) {
				return order * tkst[datatype].compare(TableKit.getCellText(a.cells[index]),TableKit.getCellText(b.cells[index]));
			});
		}
		var tb = table.tBodies[0];
		var tkr = TableKit.Rows;
		rows.each(function(r,i) {
			tb.appendChild(r);
			tkr.addStripeClass(table,r,i);
		});
		var hcells = TableKit.getHeaderCells(null, cell);
		$A(hcells).each(function(c,i){
			c = $(c);
			c.removeClassName(op.ascendingClass);
			c.removeClassName(op.descendingClass);
			if(index === i) {
				if(order === 1) {
					c.addClassName(op.ascendingClass);
				} else {
					c.addClassName(op.descendingClass);
				}
			}
		});
	},
	types : {},
	detectors : [],
	addSortType : function() {
		$A(arguments).each(function(o){
			TableKit.Sortable.types[o.name] = o;
		});
	},
	getDataType : function(cell,index,table) {
		cell = $(cell);
		index = (index || index === 0) ? index : TableKit.getCellIndex(cell);
		
		var colcache = TableKit.Sortable._coltypecache;
		var cache = colcache[table.id] ? colcache[table.id] : (colcache[table.id] = {});
		
		if(!cache[index]) {
			var t = false;
			// first look for a data type id on the heading row cell
			if(cell.id && TableKit.Sortable.types[cell.id]) {
				t = cell.id
			}
			if(!t) {
  			t = $w(cell.className).detect(function(n){ // then look for a data type classname on the heading row cell
  				return (TableKit.Sortable.types[n]) ? true : false;
  			});
			}
			if(!t) {
				var rows = TableKit.getBodyRows(table);
				cell = rows[0].cells[index]; // grab same index cell from body row to try and match data type
				t = TableKit.Sortable.detectors.detect(
						function(d){
							return TableKit.Sortable.types[d].detect(TableKit.getCellText(cell));
						});
			}
			cache[index] = t;
		}
		return cache[index];
	},
	_coltypecache : {}
};

TableKit.Sortable.detectors = $A($w('date-iso date date-eu date-au time currency datasize number casesensitivetext text')); // setting it here because Safari complained when I did it above...

TableKit.Sortable.Type = Class.create();
TableKit.Sortable.Type.prototype = {
	initialize : function(name, options){
		this.name = name;
		options = Object.extend({
			normal : function(v){
				return v;
			},
			pattern : /.*/
		}, options || {});
		this.normal = options.normal;
		this.pattern = options.pattern;
		if(options.compare) {
			this.compare = options.compare;
		}
		if(options.detect) {
			this.detect = options.detect;
		}
	},
	compare : function(a,b){
		return TableKit.Sortable.Type.compare(this.normal(a), this.normal(b));
	},
	detect : function(v){
		return this.pattern.test(v);
	}
};

TableKit.Sortable.Type.compare = function(a,b) {
	return a < b ? -1 : a === b ? 0 : 1;
};

TableKit.Sortable.addSortType(
	new TableKit.Sortable.Type('number', {
		pattern : /^[-+]?[\d]*\.?[\d]+(?:[eE][-+]?[\d]+)?/,
		normal : function(v) {
			// This will grab the first thing that looks like a number from a string, so you can use it to order a column of various srings containing numbers.
			v = parseFloat(v.replace(/^.*?([-+]?[\d]*\.?[\d]+(?:[eE][-+]?[\d]+)?).*$/,"$1"));
			return isNaN(v) ? 0 : v;
		}}),
	new TableKit.Sortable.Type('text',{
		normal : function(v) {
			return v ? v.toLowerCase() : '';
		}}),
	new TableKit.Sortable.Type('casesensitivetext',{pattern : /^[A-Z]+$/}),
	new TableKit.Sortable.Type('datasize',{
		pattern : /^[-+]?[\d]*\.?[\d]+(?:[eE][-+]?[\d]+)?\s?[k|m|g|t]b$/i,
		normal : function(v) {
			var r = v.match(/^([-+]?[\d]*\.?[\d]+([eE][-+]?[\d]+)?)\s?([k|m|g|t]?b)?/i);
			var b = r[1] ? Number(r[1]).valueOf() : 0;
			var m = r[3] ? r[3].substr(0,1).toLowerCase() : '';
			var result = b;
			switch(m) {
				case  'k':
					result = b * 1024;
					break;
				case  'm':				
					result = b * 1024 * 1024;
					break;
				case  'g':
					result = b * 1024 * 1024 * 1024;
					break;
				case  't':
					result = b * 1024 * 1024 * 1024 * 1024;
					break;
			}
			return result;
		}}),
	new TableKit.Sortable.Type('date-au',{
		pattern : /^\d{2}\/\d{2}\/\d{4}\s?(?:\d{1,2}\:\d{2}(?:\:\d{2})?\s?[a|p]?m?)?/i,
		normal : function(v) {
			if(!this.pattern.test(v)) {return 0;}
			var r = v.match(/^(\d{2})\/(\d{2})\/(\d{4})\s?(?:(\d{1,2})\:(\d{2})(?:\:(\d{2}))?\s?([a|p]?m?))?/i);
			var yr_num = r[3];
			var mo_num = parseInt(r[2],10)-1;
			var day_num = r[1];
			var hr_num = r[4] ? r[4] : 0;
			if(r[7]) {
				var chr = parseInt(r[4],10);
				if(r[7].toLowerCase().indexOf('p') !== -1) {
					hr_num = chr < 12 ? chr + 12 : chr;
				} else if(r[7].toLowerCase().indexOf('a') !== -1) {
					hr_num = chr < 12 ? chr : 0;
				}
			} 
			var min_num = r[5] ? r[5] : 0;
			var sec_num = r[6] ? r[6] : 0;
			return new Date(yr_num, mo_num, day_num, hr_num, min_num, sec_num, 0).valueOf();
		}}),
	new TableKit.Sortable.Type('date-us',{
		pattern : /^\d{2}\/\d{2}\/\d{4}\s?(?:\d{1,2}\:\d{2}(?:\:\d{2})?\s?[a|p]?m?)?/i,
		normal : function(v) {
			if(!this.pattern.test(v)) {return 0;}
			var r = v.match(/^(\d{2})\/(\d{2})\/(\d{4})\s?(?:(\d{1,2})\:(\d{2})(?:\:(\d{2}))?\s?([a|p]?m?))?/i);
			var yr_num = r[3];
			var mo_num = parseInt(r[1],10)-1;
			var day_num = r[2];
			var hr_num = r[4] ? r[4] : 0;
			if(r[7]) {
				var chr = parseInt(r[4],10);
				if(r[7].toLowerCase().indexOf('p') !== -1) {
					hr_num = chr < 12 ? chr + 12 : chr;
				} else if(r[7].toLowerCase().indexOf('a') !== -1) {
					hr_num = chr < 12 ? chr : 0;
				}
			} 
			var min_num = r[5] ? r[5] : 0;
			var sec_num = r[6] ? r[6] : 0;
			return new Date(yr_num, mo_num, day_num, hr_num, min_num, sec_num, 0).valueOf();
		}}),
	new TableKit.Sortable.Type('date-eu',{
		pattern : /^\d{2}-\d{2}-\d{4}/i,
		normal : function(v) {
			if(!this.pattern.test(v)) {return 0;}
			var r = v.match(/^(\d{2})-(\d{2})-(\d{4})/);
			var yr_num = r[3];
			var mo_num = parseInt(r[2],10)-1;
			var day_num = r[1];
			return new Date(yr_num, mo_num, day_num).valueOf();
		}}),
	new TableKit.Sortable.Type('date-iso',{
		pattern : /[\d]{4}-[\d]{2}-[\d]{2}(?:T[\d]{2}\:[\d]{2}(?:\:[\d]{2}(?:\.[\d]+)?)?(Z|([-+][\d]{2}:[\d]{2})?)?)?/, // 2005-03-26T19:51:34Z
		normal : function(v) {
			if(!this.pattern.test(v)) {return 0;}
		    var d = v.match(/([\d]{4})(-([\d]{2})(-([\d]{2})(T([\d]{2}):([\d]{2})(:([\d]{2})(\.([\d]+))?)?(Z|(([-+])([\d]{2}):([\d]{2})))?)?)?)?/);		
		    var offset = 0;
		    var date = new Date(d[1], 0, 1);
		    if (d[3]) { date.setMonth(d[3] - 1) ;}
		    if (d[5]) { date.setDate(d[5]); }
		    if (d[7]) { date.setHours(d[7]); }
		    if (d[8]) { date.setMinutes(d[8]); }
		    if (d[10]) { date.setSeconds(d[10]); }
		    if (d[12]) { date.setMilliseconds(Number("0." + d[12]) * 1000); }
		    if (d[14]) {
		        offset = (Number(d[16]) * 60) + Number(d[17]);
		        offset *= ((d[15] === '-') ? 1 : -1);
		    }
		    offset -= date.getTimezoneOffset();
		    if(offset !== 0) {
		    	var time = (Number(date) + (offset * 60 * 1000));
		    	date.setTime(Number(time));
		    }
			return date.valueOf();
		}}),
	new TableKit.Sortable.Type('date',{
		pattern: /^(?:sun|mon|tue|wed|thu|fri|sat)\,\s\d{1,2}\s(?:jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s\d{4}(?:\s\d{2}\:\d{2}(?:\:\d{2})?(?:\sGMT(?:[+-]\d{4})?)?)?/i, //Mon, 18 Dec 1995 17:28:35 GMT
		compare : function(a,b) { // must be standard javascript date format
			if(a && b) {
				return TableKit.Sortable.Type.compare(new Date(a),new Date(b));
			} else {
				return TableKit.Sortable.Type.compare(a ? 1 : 0, b ? 1 : 0);
			}
		}}),
	new TableKit.Sortable.Type('time',{
		pattern : /^\d{1,2}\:\d{2}(?:\:\d{2})?(?:\s[a|p]m)?$/i,
		compare : function(a,b) {
			var d = new Date();
			var ds = d.getMonth() + "/" + d.getDate() + "/" + d.getFullYear() + " ";
			return TableKit.Sortable.Type.compare(new Date(ds + a),new Date(ds + b));
		}}),
	new TableKit.Sortable.Type('currency',{
		pattern : /^[$����]/, // dollar,pound,yen,euro,generic currency symbol
		normal : function(v) {
			return v ? parseFloat(v.replace(/[^-\d\.]/g,'')) : 0;
		}})
);

TableKit.Resizable = {
	init : function(elm, options){
		var table = $(elm);
		if(table.tagName !== "TABLE") {return;}
		TableKit.register(table,Object.extend(options || {},{resizable:true}));		 
		var cells = TableKit.getHeaderCells(table);
		cells.each(function(c){
			c = $(c);
			Event.observe(c, 'mouseover', TableKit.Resizable.initDetect);
			Event.observe(c, 'mouseout', TableKit.Resizable.killDetect);
		});
	},
	resize : function(table, index, w) {
		var cell;
		if(typeof index === 'number') {
			if(!table || (table.tagName && table.tagName !== "TABLE")) {return;}
			table = $(table);
			index = Math.min(table.rows[0].cells.length, index);
			index = Math.max(1, index);
			index -= 1;
			cell = (table.tHead && table.tHead.rows.length > 0) ? $(table.tHead.rows[table.tHead.rows.length-1].cells[index]) : $(table.rows[0].cells[index]);
		} else {
			cell = $(index);
			table = table ? $(table) : cell.up('table');
			index = TableKit.getCellIndex(cell);
		}
		var pad = parseInt(cell.getStyle('paddingLeft'),10) + parseInt(cell.getStyle('paddingRight'),10);
		w = Math.max(w-pad, TableKit.option('minWidth', table.id)[0]);
		
		cell.setStyle({'width' : w + 'px'});
	},
	initDetect : function(e) {
		e = TableKit.e(e);
		var cell = Event.element(e);
		Event.observe(cell, 'mousemove', TableKit.Resizable.detectHandle);
		Event.observe(cell, 'mousedown', TableKit.Resizable.startResize);
	},
	detectHandle : function(e) {
		e = TableKit.e(e);
		var cell = Event.element(e);
  		if(TableKit.Resizable.pointerPos(cell,Event.pointerX(e),Event.pointerY(e))){
  			cell.addClassName(TableKit.option('resizeOnHandleClass', cell.up('table').id)[0]);
  			TableKit.Resizable._onHandle = true;
  		} else {
  			cell.removeClassName(TableKit.option('resizeOnHandleClass', cell.up('table').id)[0]);
  			TableKit.Resizable._onHandle = false;
  		}
	},
	killDetect : function(e) {
		e = TableKit.e(e);
		TableKit.Resizable._onHandle = false;
		var cell = Event.element(e);
		Event.stopObserving(cell, 'mousemove', TableKit.Resizable.detectHandle);
		Event.stopObserving(cell, 'mousedown', TableKit.Resizable.startResize);
		cell.removeClassName(TableKit.option('resizeOnHandleClass', cell.up('table').id)[0]);
	},
	startResize : function(e) {
		e = TableKit.e(e);
		if(!TableKit.Resizable._onHandle) {return;}
		var cell = Event.element(e);
		Event.stopObserving(cell, 'mousemove', TableKit.Resizable.detectHandle);
		Event.stopObserving(cell, 'mousedown', TableKit.Resizable.startResize);
		Event.stopObserving(cell, 'mouseout', TableKit.Resizable.killDetect);
		TableKit.Resizable._cell = cell;
		var table = cell.up('table');
		TableKit.Resizable._tbl = table;
		if(TableKit.option('showHandle', table.id)[0]) {
			TableKit.Resizable._handle = $(document.createElement('div')).addClassName('resize-handle').setStyle({
				'top' : cell.cumulativeOffset()[1] + 'px',
				'left' : Event.pointerX(e) + 'px',
				'height' : table.getDimensions().height + 'px'
			});
			document.body.appendChild(TableKit.Resizable._handle);
		}
		Event.observe(document, 'mousemove', TableKit.Resizable.drag);
		Event.observe(document, 'mouseup', TableKit.Resizable.endResize);
		Event.stop(e);
	},
	endResize : function(e) {
		e = TableKit.e(e);
		var cell = TableKit.Resizable._cell;
		TableKit.Resizable.resize(null, cell, (Event.pointerX(e) - cell.cumulativeOffset()[0]));
		Event.stopObserving(document, 'mousemove', TableKit.Resizable.drag);
		Event.stopObserving(document, 'mouseup', TableKit.Resizable.endResize);
		if(TableKit.option('showHandle', TableKit.Resizable._tbl.id)[0]) {
			$$('div.resize-handle').each(function(elm){
				document.body.removeChild(elm);
			});
		}
		Event.observe(cell, 'mouseout', TableKit.Resizable.killDetect);
		TableKit.Resizable._tbl = TableKit.Resizable._handle = TableKit.Resizable._cell = null;
		Event.stop(e);
	},
	drag : function(e) {
		e = TableKit.e(e);
		if(TableKit.Resizable._handle === null) {
			try {
				TableKit.Resizable.resize(TableKit.Resizable._tbl, TableKit.Resizable._cell, (Event.pointerX(e) - TableKit.Resizable._cell.cumulativeOffset()[0]));
			} catch(e) {}
		} else {
			TableKit.Resizable._handle.setStyle({'left' : Event.pointerX(e) + 'px'});
		}
		return false;
	},
	pointerPos : function(element, x, y) {
    	var offset = $(element).cumulativeOffset();
	    return (y >= offset[1] &&
	            y <  offset[1] + element.offsetHeight &&
	            x >= offset[0] + element.offsetWidth - 5 &&
	            x <  offset[0] + element.offsetWidth);
  	},
	_onHandle : false,
	_cell : null,
	_tbl : null,
	_handle : null
};


TableKit.Editable = {
	init : function(elm, options){
		var table = $(elm);
		if(table.tagName !== "TABLE") {return;}
		TableKit.register(table,Object.extend(options || {},{editable:true}));
		Event.observe(table.tBodies[0], 'click', TableKit.Editable._editCell);
	},
	_editCell : function(e) {
		e = TableKit.e(e);
		var cell = Event.findElement(e,'td');
		if(cell) {
			TableKit.Editable.editCell(null, cell, null, e);
		} else {
			return false;
		}
	},
	editCell : function(table, index, cindex, event) {
		var cell, row;
		if(typeof index === 'number') {
			if(!table || (table.tagName && table.tagName !== "TABLE")) {return;}
			table = $(table);
			index = Math.min(table.tBodies[0].rows.length, index);
			index = Math.max(1, index);
			index -= 1;
			cindex = Math.min(table.rows[0].cells.length, cindex);
			cindex = Math.max(1, cindex);
			cindex -= 1;
			row = $(table.tBodies[0].rows[index]);
			cell = $(row.cells[cindex]);
		} else {
			cell = $(event ? Event.findElement(event, 'td') : index);
			table = (table && table.tagName && table.tagName !== "TABLE") ? $(table) : cell.up('table');
			row = cell.up('tr');
		}
		var op = TableKit.option('noEditClass', table.id);
		if(cell.hasClassName(op.noEditClass)) {return;}
		
		var head = $(TableKit.getHeaderCells(table, cell)[TableKit.getCellIndex(cell)]);
		if(head.hasClassName(op.noEditClass)) {return;}
		
		var data = TableKit.getCellData(cell);
		if(data.active) {return;}
		data.htmlContent = cell.innerHTML;
		var ftype = TableKit.Editable.getCellEditor(null,null,head);
		ftype.edit(cell, event);
		data.active = true;
	},
	getCellEditor : function(cell, table, head) {
	  var head = head ? head : $(TableKit.getHeaderCells(table, cell)[TableKit.getCellIndex(cell)]);
	  var ftype = TableKit.Editable.types['text-input'];
		if(head.id && TableKit.Editable.types[head.id]) {
			ftype = TableKit.Editable.types[head.id];
		} else {
			var n = $w(head.className).detect(function(n){
					return (TableKit.Editable.types[n]) ? true : false;
			});
			ftype = n ? TableKit.Editable.types[n] : ftype;
		}
		return ftype;
	},
	types : {},
	addCellEditor : function(o) {
		if(o && o.name) { TableKit.Editable.types[o.name] = o; }
	}
};

TableKit.Editable.CellEditor = Class.create();
TableKit.Editable.CellEditor.prototype = {
	initialize : function(name, options){
		this.name = name;
		this.options = Object.extend({
			element : 'input',
			attributes : {name : 'value', type : 'text'},
			selectOptions : [],
			showSubmit : true,
			submitText : 'OK',
			showCancel : true,
			cancelText : 'Cancel',
			ajaxURI : null,
			ajaxOptions : null
		}, options || {});
	},
	edit : function(cell) {
		cell = $(cell);
		var op = this.options;
		var table = cell.up('table');
		
		var form = $(document.createElement("form"));
		form.id = cell.id + '-form';
		form.addClassName(TableKit.option('formClassName', table.id)[0]);
		form.onsubmit = this._submit.bindAsEventListener(this);
		
		var field = document.createElement(op.element);
			$H(op.attributes).each(function(v){
				field[v.key] = v.value;
			});
			switch(op.element) {
				case 'input':
				case 'textarea':
				field.value = TableKit.getCellText(cell);
				break;
				
				case 'select':
				var txt = TableKit.getCellText(cell);
				$A(op.selectOptions).each(function(v){
					field.options[field.options.length] = new Option(v[0], v[1]);
					if(txt === v[1]) {
						field.options[field.options.length-1].selected = 'selected';
					}
				});
				break;
			}
			form.appendChild(field);
			if(op.element === 'textarea') {
				form.appendChild(document.createElement("br"));
			}
			if(op.showSubmit) {
				var okButton = document.createElement("input");
				okButton.type = "submit";
				okButton.value = op.submitText;
				okButton.className = 'editor_ok_button';
				form.appendChild(okButton);
			}
			if(op.showCancel) {
				var cancelLink = document.createElement("a");
				cancelLink.href = "#";
				cancelLink.appendChild(document.createTextNode(op.cancelText));
				cancelLink.onclick = this._cancel.bindAsEventListener(this);
				cancelLink.className = 'editor_cancel';      
				form.appendChild(cancelLink);
			}
			cell.innerHTML = '';
			cell.appendChild(form);
	},
	_submit : function(e) {
		var cell = Event.findElement(e,'td');
		var form = Event.findElement(e,'form');
		Event.stop(e);
		this.submit(cell,form);
	},
	submit : function(cell, form) {
		var op = this.options;
		form = form ? form : cell.down('form');
		var head = $(TableKit.getHeaderCells(null, cell)[TableKit.getCellIndex(cell)]);
		var row = cell.up('tr');
		var table = cell.up('table');
		var s = '&row=' + (TableKit.getRowIndex(row)+1) + '&cell=' + (TableKit.getCellIndex(cell)+1) + '&id=' + row.id + '&field=' + head.id + '&' + Form.serialize(form);
		this.ajax = new Ajax.Updater(cell, op.ajaxURI || TableKit.option('editAjaxURI', table.id)[0], Object.extend(op.ajaxOptions || TableKit.option('editAjaxOptions', table.id)[0], {
			postBody : s,
			onComplete : function() {
				var data = TableKit.getCellData(cell);
				data.active = false;
				data.refresh = true; // mark cell cache for refreshing, in case cell contents has changed and sorting is applied
			}
		}));
	},
	_cancel : function(e) {
		var cell = Event.findElement(e,'td');
		Event.stop(e);
		this.cancel(cell);
	},
	cancel : function(cell) {
		this.ajax = null;
		var data = TableKit.getCellData(cell);
		cell.innerHTML = data.htmlContent;
		data.htmlContent = '';
		data.active = false;
	},
	ajax : null
};

TableKit.Editable.textInput = function(n,attributes) {
	TableKit.Editable.addCellEditor(new TableKit.Editable.CellEditor(n, {
		element : 'input',
		attributes : Object.extend({name : 'value', type : 'text'}, attributes||{})
	}));
};
TableKit.Editable.textInput('text-input');

TableKit.Editable.multiLineInput = function(n,attributes) {
	TableKit.Editable.addCellEditor(new TableKit.Editable.CellEditor(n, {
		element : 'textarea',
		attributes : Object.extend({name : 'value', rows : '5', cols : '20'}, attributes||{})
	}));	
};	
TableKit.Editable.multiLineInput('multi-line-input');

TableKit.Editable.selectInput = function(n,attributes,selectOptions) {
	TableKit.Editable.addCellEditor(new TableKit.Editable.CellEditor(n, {
		element : 'select',
		attributes : Object.extend({name : 'value'}, attributes||{}),
		'selectOptions' : selectOptions
	}));	
};

/*
TableKit.Bench = {
	bench : [],
	start : function(){
		TableKit.Bench.bench[0] = new Date().getTime();
	},
	end : function(s){
		TableKit.Bench.bench[1] = new Date().getTime();
		alert(s + ' ' + ((TableKit.Bench.bench[1]-TableKit.Bench.bench[0])/1000)+' seconds.') //console.log(s + ' ' + ((TableKit.Bench.bench[1]-TableKit.Bench.bench[0])/1000)+' seconds.')
		TableKit.Bench.bench = [];
	}
} */

document.observe("dom:loaded", TableKit.load);

	if (ConfigurationParser === undefined) {
		var ConfigurationParser = {};
	}
	
	ConfigurationParser.defaultVars = {
		buttonClass: 'configuration-parser-btn',
		infoBlockClass: 'configuration-parser-info-block',
		containerClass: 'configuration-parser-container'
	};
	
	if (ConfigurationParser.vars === undefined) {
		ConfigurationParser.vars = {};
	}
	
	for (var key in ConfigurationParser.defaultVars) {
		if (!ConfigurationParser.defaultVars.hasOwnProperty(key)) {
			continue;
		}
		
		if (ConfigurationParser.vars[key] === undefined) {
			ConfigurationParser.vars[key] = ConfigurationParser.defaultVars[key];
		}
	}
	
	ConfigurationParser._uniqid = function(base, index) {
		if (index === undefined) {
			index = 0;
		}
		
		if ($(base)) {
			if ($(base+'_'+(index +1))) {
				return ConfigurationParser._uniqid(base, index +1);
			} else {
				return base+'_'+(index +1);
			}
		} else {
			return base;
		}
	};
	
	ConfigurationParser.incrustationInfo = function(selector, json) {
		$$(selector).each(function(element) {
			var key = element.innerHTML.trim(),
				infoButton,
				infoBlock,
				id,
				comment,
				exploded,
				i,
				multikey = [];

			if (json[key] !== undefined) {
				comment = json[key].comment;
			} else if (key.indexOf('.')) {
				exploded = key.split('.');
				for (i = 0; i < exploded.length -1; i++) {
					multikey.push(exploded[i]);
					if (json[multikey.join('.')] !== undefined) {
						comment = json[multikey.join('.')].comment;
					}
				}
			}

			if (comment) {
				id = ConfigurationParser._uniqid('info-'+key.replace(/\./g, '-'));
				element.addClassName(ConfigurationParser.vars.containerClass);
				
				infoButton = new Element('div', {'class': ConfigurationParser.vars.buttonClass, 'for': id});
				infoButton.observe('click', function(event){
					var target = $(event.target.getAttribute('for'));
					console.log($(target));
					$(event.target.getAttribute('for')).toggle();
					event.target.toggleClassName('active');
				});
				infoBlock = new Element('div', {'class': ConfigurationParser.vars.infoBlockClass, 'id': id});
				infoBlock.insert(comment).hide();

				element.innerHTML = '';
				element.insert(infoButton)
					.insert(key)
					.insert(infoBlock);
			}
		});
	};