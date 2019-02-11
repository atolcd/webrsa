<?php
	App::uses( 'ProgressBarTask', 'Console/Command/Task' );

	/*
	 *
	 *
	 * CakePHP shell task for doing a simple progress bar
	 * Copyright (c) 2010 Matt Curry
	 * www.PseudoCoder.com
	 * http://github.com/mcurry/progress_bar
	 *
	 * @author      Matt Curry <matt@pseudocoder.com>
	 * @license     MIT
	 *
	 */
	/**
	 * ProgressBarTask class
	 *
	 * @uses          Shell
	 * @package       progress_bar
	 * @subpackage    progress_bar.Vendor.shells.tasks
	 */
	class XProgressBarTask extends ProgressBarTask
	{

		/**
		 * Increment the progress
		 *
		 * @return void
		 * @access public
		 */
		public function next( $inc = 1, $additionalInfos = '' ) {
			$this->done += $inc;
			$this->set( null, null, $additionalInfos );
		}

		/**
		 * set method
		 *
		 * @param string $done Amount completed
		 * @param string $doneSize bar size
		 * @return void
		 * @access public
		 */
		public function set( $done = null, $doneSize = null, $additionalInfos = '' ) {
			if( $done ) {
				$this->done = min( $done, $this->total );
			}

			$this->total = max( 1, $this->total );
			$perc = round( $this->done / $this->total, 3 );
			if( $doneSize === null ) {
				$doneSize = floor( min( $perc, 1 ) * $this->size );
			}

			$output = sprintf(
					"[%s>%s] %.01f%% %d/%d %s %s", str_repeat( "-", $doneSize ), str_repeat( " ", $this->size - $doneSize ), $perc * 100, $this->done, $this->total, $this->_niceRemaining(), __( 'remaining', true ), $this->done, $this->total
			);



			$output = str_pad( $output.( $additionalInfos != '' ? ' [ '.$additionalInfos.' ]' : ''), (int) $this->terminalWidth, ' ', STR_PAD_RIGHT );
			$this->out( "\r".$output );
			flush();
		}

		/**
		 * Calculate remaining time in a nice format
		 *
		 * @return void
		 * @access public
		 */
		protected function _niceRemaining() {
			$now = time();
			if( $now == $this->startTime || $this->done == 0 ) {
				return '?';
			}

			$rate = ($this->startTime - $now) / $this->done;
			$remaining = -1 * round( $rate * ($this->total - $this->done) );

			if( $remaining < 60 ) {
				return sprintf( '%02d %s', $remaining, __n( 'sec', 'secs', $remaining, true ) );
			}
			else {
				return sprintf( '%d %s, %02d %s', floor( $remaining / 60 ), __n( 'min', 'mins', floor( $remaining / 60 ), true ), $remaining % 60, __n( 'sec', 'secs', $remaining % 60, true ) );
			}
		}

	}
?>
