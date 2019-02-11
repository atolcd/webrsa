<?php

/**
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * 
 * Suspendisse accumsan turpis bibendum nisl pharetra, ut condimentum ante 
 * porttitor.
 * 
 * Etiam ullamcorper mollis dui, eget ornare orci ultricies vel. 
 * Vivamus facilisis varius massa, id bibendum tortor ullamcorper vel.
 * 
 * Nulla et rhoncus nisi. Vivamus feugiat ultrices rutrum. Donec ornare eget 
 * odio ut eleifend. Fusce rhoncus congue elit in pulvinar. Curabitur cursus 
 * ante libero. Donec a elementum nulla. Nam non diam ipsum. Nam quis sem id 
 * velit semper mattis. Suspendisse eu metus vitae odio maximus sollicitudin 
 * ac in dui. Mauris mollis, diam nec egestas tempor, nisl ipsum rutrum lectus, 
 * in porttitor odio justo sit amet nibh. Phasellus vitae dictum justo. 
 */

Configure::write('String.test', true);


// Lorem ipsum dolor sit amet

/**
 * Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, 
 * consectetur, adipisci velit...
 * 
 * @meta string		Comment
 * @meta array		Another comment
 *					with multi-line
 * 
 * @meta integer	last comment
 */
Configure::write(
	'isTest',
	array(
		'param1' => true,
		'param2' => false,
	)
);

/**
 * Consecutive test
 */
Configure::write('key1', true);
Configure::write('key2', false);

$test = (int)'155';
Configure::write('key3', $test);

/**
 * Odd test
 */
Configure


	::
	
	
		write
	(
				'anotherTest'
			,
			
			array(
1 => 'foo'				
	)
			)
	
	
	;

/**
 * No comment
 */
/**
 * Not yet
 */
/**
 * this is a comment
 */
Configure::write('foo', 'bar');

/*
 * No match (miss a *)
 */
Configure::write('baz', 'bar');