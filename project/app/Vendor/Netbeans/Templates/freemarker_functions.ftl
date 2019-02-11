<#--
	@see http://freemarker.sourceforge.net/docs/dgui_template_exp.html#dgui_template_exp_stringop_interpolation
	@see http://freemarker.sourceforge.net/docs/ref_builtins_string.html
	@see http://wiki.netbeans.org/FaqFreeMarker
-->

<#function class_name name>
	<#local tmp = ''>
	<#local splits = name?uncap_first?replace("([A-Z])", "_$1", "r")?split('_')>
	<#list splits as split>
		<#local tmp = tmp + split?capitalize>
	</#list>
	<#return tmp>
</#function>

<#function underscore name>
	<#return class_name(name)?replace("([a-z0-9])([A-Z])", "$1_$2", "r")?lower_case>
</#function>

<#function foreign_key name>
  <#return underscore(name) + '_id'>
</#function>