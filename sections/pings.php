<?php

namespace sgkirby\SendMentions;

return [

	'props' => [
	
		'headline' => function ($message = "Pings sent") {
			return $message;
		}
		
	],
	
	'computed' => [
	
		'pings' => function () {
			
			$logfile = $this->model()->root() . DS . '.sendmentions.json';
			
			if( file_exists( $logfile ) ) {
				
				$return = '<table>';
				foreach( \Kirby\Data\Data::read( $logfile ) as $url => $pings ) :
					foreach ( $pings as $type => $data ):
						if ( $type == 'webmention' )
							$return .= '<tr><td>' . $url . '</td><td>' . $data['response'] . '</td></tr>';
						elseif ( $type == 'archive.org' )
							$return .= '<tr><td colspan="2">' . $url . ' <a href="' . $data['url'] . '" target="_blank">(' . $type . ')</a></td></tr>';
						else
							$return .= '<tr><td colspan="2">' . $url . ' (' . $type . ')</td></tr>';
					endforeach;
				endforeach;
				$return .= '</table>';
				
				return $return;
				
			} else {
				
				return '<p>No pings sent</p>';
				
			}
		}
		
	],

];
