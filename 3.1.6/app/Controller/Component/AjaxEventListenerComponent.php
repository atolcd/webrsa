<?php
	/*
		// Mouse Events
		onClick 	The event occurs when the user clicks on an element
		onDblClick 	The event occurs when the user double-clicks on an element
		onMouseDown 	The event occurs when a user presses a mouse button over an element
		onMouseMove 	The event occurs when the pointer is moving while it is over an element
		onMouseOver 	The event occurs when the pointer is moved onto an element
		onMouseOut 	The event occurs when a user moves the mouse pointer out of an element
		onMouseUp 	The event occurs when a user releases a mouse button over an element

		// Keyboard Events
		onKeyDown 	The event occurs when the user is pressing a key
		onKeyPress 	The event occurs when the user presses a key
		onKeyUp 	The event occurs when the user releases a key

		// Frame/Object Events
		onAbort 	The event occurs when an image is stopped from loading before completely loaded (for <object>)
		onError 	The event occurs when an image does not load properly (for <object>, <body> and <frameset>) 	 
		onLoad 	The event occurs when a document, frameset, or <object> has been loaded
		onResize 	The event occurs when a document view is resized
		onScroll 	The event occurs when a document view is scrolled
		onUnload 	The event occurs once a page has unloaded (for <body> and <frameset>)

		// Form Events
		onBlur 	The event occurs when a form element loses focus
		onChange 	The event occurs when the content of a form element, the selection, or the checked state have changed (for <input>, <select>, and <textarea>)
		onFocus 	The event occurs when an element gets focus (for <label>, <input>, <select>, textarea>, and <button>)
		onReset 	The event occurs when a form is reset
		onSelect 	The event occurs when a user selects some  text (for <input> and <textarea>)
		onSubmit
	*/
	class AjaxEventListenerComponent extends Component
	{
		/**
		 *
		 */
		public $eventMap = array(
			'dataavailable' => 'onLoad',
			'onkeyup' => 'onKeyUp',
			'onchange' => 'onChange',
		);

		/**
		 * Keyboard Events. The event occurs when the user releases a key.
		 */
		public function onKeyUp() {
		}
		

		/**
		 * Form Events. The event occurs when the content of a form element,
		 * the selection, or the checked state have changed (for <input>, <select>,
		 * and <textarea>).
		 */
		public function onChange() {
		}
	}
?>