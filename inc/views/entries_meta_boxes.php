<ul id='starter-entry' style='display:none'>
	<li class="ui-state-default entry">
		<?php render_toolbar(); ?>
		<div class="info">
			<?php
				$starterID = '{changeStarterID}';
				field_type_input(array(  
		        	'label'=> __('Title','mycontest'),  
		        	'desc'  => __('A description for the field.','mycontest'),  
		        	'id'    => 'entryTitle',
		        	'type' => 'text',
		        	'entryid' => $starterID,
		        	'meta' =>  ''
		    	));
		    	field_type_input(array(  
		        	'label'=> __('URL','mycontest'),  
		        	'desc'  => __('A description for the field.','mycontest'),  
		        	'id'    => 'url',
		        	'type' => 'text',
		        	'entryid' => $starterID,
		        	'meta' => ''
		    	));
				field_type_input(array(  
		        	'label'=> 'entryID',  
		        	'desc'  => '',  
		        	'id'    => 'entryID',
		        	'type' => 'hidden',
		        	'entryid' => $starterID,
		        	'meta' => $starterID
		    	));
				field_type_input(array(  
		        	'label'=> __('Author','mycontest'),  
		        	'desc'  => __('A description for the field.','mycontest'),  
		        	'id'    => 'author',
		        	'type' => 'text',
		        	'entryid' => $starterID,
		        	'meta' => ''
		    	));
		    	field_type_input(array(  
		        	'label'=> __('Author URL','mycontest'),  
		        	'desc'  => __('A description for the field.','mycontest'),  
		        	'id'    => 'authorurl',
		        	'type' => 'text',
		        	'entryid' => $starterID,
		        	'meta' => ''
		    	));
		    	
		    	field_type_input(array(  
		        	'label'=> __('Votes','mycontest'),  
		        	'desc'  => __('A description for the field.','mycontest'),  
		        	'id'    => 'votes',
		        	'type' => 'number',
		        	'entryid' => $starterID,
		        	'meta' => 0
		    	));
		    	field_type_textarea(array(  
		        	'label'=> __('Description/other (html allowed)','mycontest'),  
		        	'desc'  => __('Description for entry.','mycontest'),  
		        	'id'    => 'descr',
		        	'type' => 'text',
		        	'entryid' => $starterID,
		        	'meta' => ''
		    	));
		    ?>
		</div>
		<div class="thumb">
			<?php
				field_type_image(array(  
				    'label'=> __('Photo','mycontest'),  
				    'desc'  => __('A description for the field.','mycontest'),  
				    'id'    => 'img_url',
				    'entryid' => $starterID,
				    'meta' => '{starterImage}'
				));
			?>
		</div>
	</li>
</ul>
	
<?php 
// Get the meta data
$meta = get_post_meta($post->ID, '_myContest', TRUE);

// Send meta data to the js console to view easy and out of the way 
// if debug is on
if($this->debug) echo"<script type = 'text/javascript'>console.log(" . json_encode($meta) . ");</script>";
?>	
<div id="mycontest-loading"><h2><?php _e('myContest is loading...','mycontest'); ?></h2><p><?php _e('Make sure javascript is enabled','mycontest'); ?></p></div>
<div id="mycontest-container" style="display:none;">

	<!-- <div id="mycontest-settings"> -->
		<?php
		// Use nonce for verification
		wp_nonce_field('save_entries','mycontest-nonce');

		if( !$meta ){ 
			// If adding post, there is no data to get
			// thus these are default values
			// $meta_values = array('enddate'=>date('m/d/Y'),'endh'=>'12','endmn'=>'00'); today
			$meta_values = array(
				'votenoshow'=>'',
				's_date'=>'',
				's_h'=>'',
				's_mn'=>'',
				's_txt'=>'',
				'e_date'=>'',
				'e_h'=>'',
				'e_mn'=>'',
				'e_txt'=>'',
				'e_showentries'=>'',
				'sort'=>'none',
				'regvoteonly'=>false,
				'regvoteonlyshow'=>false,
				'regvoteonlyhtml'=>''
			);
		}else{
			// Set our meta values
			$meta_values = $meta['settings'];
		}
		?>
		<div id="tabbed-nav">
	  		<ul>
	  			<li><a><?php _e('Entries','mycontest'); ?></a></li>
		        <li><a><?php _e('General','mycontest'); ?></a></li>
				<li><a><?php _e('Voting Start','mycontest'); ?></a></li>
				<li><a><?php _e('Voting End','mycontest'); ?></a></li>
				<li><a><?php _e('Social','mycontest'); ?></a></li>
				<li><a><?php _e('Help','mycontest'); ?></a></li>
				<?php if($this->debug){ ?><li><a><?php _e('Debug','mycontest'); ?></a></li><?php } /* end debug */ ?>
			</ul>
			<div>
				<!-- ####### Entries ####### -->
				<div class="tab-content">
					<div class="control_area">
						<input class='myContest-add-new-entry button button-primary button-large' type='button' value='<?php _e('Add New Entry','mycontest'); ?>' />
						<input class='myContest-undo-delete-entry button button-large' style="display:none" type='button' value='<?php _e('Undo','mycontest'); ?>' />
					</div>

					<div class="indented_area">
						<ul id="sortable">
							<h2 id='no-entries' style="display:none"><?php _e('It is lonely here.','mycontest');?><br/><?php _e('Add a entry by clicking Add New Entry above.','mycontest');?></h2>

							<?php
							
							if( isset($meta['entries']) ):

							foreach($meta['entries'] as $entry):
								if(!isset($entry['entryID'])) $entry['entryID'] = uniqid();
							?>
								<li class="ui-state-default entry" id="<?php echo $entry['entryID']; ?>">
									<?php render_toolbar(); ?>

									<div class="info">
								<?php
										field_type_input(array(  
								        	'label'=> 'entryID',  
								        	'desc'  => '',  
								        	'id'    => 'entryID',
								        	'type' => 'hidden',
								        	'entryid' => $entry['entryID'],
								        	'meta' => $entry['entryID']
								    	));
								    	field_type_input(array(  
								        	'label'=> __('Title','mycontest'),
								        	'desc'  => __('A description for the field.','mycontest'),  
								        	'id'    => 'entryTitle',
								        	'type' => 'text',
								        	'entryid' => $entry['entryID'],
								        	'meta' =>  $entry['entryTitle']
								    	));
								    	field_type_input(array(  
								        	'label'=> __('URL','mycontest'),  
								        	'desc'  => __('A description for the field.','mycontest'),  
								        	'id'    => 'url',
								        	'type' => 'text',
								        	'entryid' => $entry['entryID'],
								        	'meta' => $entry['url']
								    	));
										field_type_input(array(  
								        	'label'=> __('Author','mycontest'),  
								        	'desc'  => __('A description for the field.','mycontest'),  
								        	'id'    => 'author',
								        	'type' => 'text',
								        	'entryid' => $entry['entryID'],
								        	'meta' => $entry['author']
								    	));
								    	// No update errors
								    	if(!isset($entry['authorurl'])){$entry['authorurl'] = "";}
								    	field_type_input(array(  
								        	'label'=> __('Author URL','mycontest'),  
								        	'desc'  => __('A description for the field.','mycontest'),  
								        	'id'    => 'authorurl',
								        	'type' => 'text',
								        	'entryid' => $entry['entryID'],
								        	'meta' => $entry['authorurl']
								    	));
								    	field_type_input(array(  
								        	'label'=> __('Votes','mycontest'),  
								        	'desc'  => __('A description for the field.','mycontest'),  
								        	'id'    => 'votes',
								        	'type' => 'number',
								        	'entryid' => $entry['entryID'],
								        	'meta' => $entry['votes']
								    	));
								    	field_type_textarea(array(  
						        			'label'=> __('Description/other (html allowed)','mycontest'),  
						        			'desc'  => __('Description for entry.','mycontest'),  
						        			'id'    => 'descr',
						        			'type' => 'text',
						        			'entryid' => $entry['entryID'],
						        			'meta' => $entry['descr']
						    			));
								?>
									</div>
									<div class="thumb">
									<?php
									    	field_type_image(array(  
									        	'label'=> __('Photo','mycontest'),  
									        	'desc'  => __('A description for the field.','mycontest'),  
									        	'id'    => 'img_url',
									        	'entryid' => $entry['entryID'],
									        	'meta' => $entry['img_url']
									    	));
									?>
									</div>	
								</li>
							<?php 
							endforeach; 
							endif;//end if entries are set
							?>
						</ul>
					</div><!-- end indented -->
					<div class="control_area" style="width:100%;float:left;">
						<input class='myContest-add-new-entry button button-primary button-large' type='button' value='<?php _e('Add New Entry','mycontest'); ?>' />
						<input class='myContest-undo-delete-entry button button-large' style="display:none" type='button' value='<?php _e('Undo','mycontest'); ?>' />
					</div>
				</div>
				<!-- ####### General ####### -->
				<div class="tab-content">

					<div class="misc-pub-section">
						<?php 

							// 86400000 

						?>
						<?php 

						if( !isset($meta_values['votetime']) || empty($meta_values['votetime'])) $meta_values['votetime'] = 1440; 

						if($meta_values['votetime'] == 86400000) $meta_values['votetime'] = 1440;


						?>
						<label for="settings[votetime]">
							<?php _e( 'Time between votes','mycontest' ) ?>
						</label>

						<select name="settings[votetime]">
							<?php 

							$votetimeselections = array(
								__("None",'mycontest') => "0",
								__("1 Minute",'mycontest') => "1",
								__("2 Minutes",'mycontest') => "2",
								__("3 Minutes",'mycontest') => "3",
								__("5 Minutes",'mycontest') => "5",
								__("10 Minutes",'mycontest') => "10",
								__("15 Minutes",'mycontest') => "15",
								__("20 Minutes",'mycontest') => "20",
								__("30 Minutes",'mycontest') => "30",
								__("45 Minutes",'mycontest') => "45",
								__("60 Minutes",'mycontest') => "60",
								__("90 Minutes",'mycontest') => "90",
								__("2 hours",'mycontest') => "120",
								__("4 hours",'mycontest') => "240",
								__("6 hours",'mycontest') => "360",
								__("8 hours",'mycontest') => "480",
								__("10 hours",'mycontest') => "600",
								__("12 hours",'mycontest') => "720",
								__("24 hours",'mycontest') => "1440",
								__("2 days",'mycontest') => "2880",
								__("3 days",'mycontest') => "4320",
								__("5 days",'mycontest') => "7200",
								__("10 days",'mycontest') => "14400",
								__("15 days",'mycontest') => "21600",
								__("30 days",'mycontest') => "43200",
								__("60 days",'mycontest') => "86400",
								__("90 days",'mycontest') => "129600",
								__("6 months (180 days)",'mycontest') => "259200",
								__("1 year (365 days)",'mycontest') => "525600",
							);

							foreach($votetimeselections as $key => $value){
								echo "<option value='$value' " . selected( $meta_values['votetime'], $value, false ) . ">" . $key . "</option>";
							}

							?>
						</select>
					</div>

					<div class="misc-pub-section">
						<label for="settings[sort]" ><?php _e( 'Sort entries:' ) ?></label>
						<?php if(isset($meta_values['a_entry_ribbons'])) $meta_values['sort'] = "high"; ?>
						<select name="settings[sort]">
							<option value="none" <?php selected( $meta_values['sort'], 'none' ); ?>><?php _e("Don't Sort",'mycontest');?></option>
							<option value="high" <?php selected( $meta_values['sort'], 'high' ); ?>><?php _e("Highest Votes First",'mycontest');?></option>
							<option value="low" <?php selected( $meta_values['sort'], 'low' ); ?>><?php _e("Lowest Votes First",'mycontest');?></option>
							<option value="aztitle" <?php selected( $meta_values['sort'], 'aztitle' ); ?>><?php _e("Title A-Z",'mycontest');?></option>
							<option value="zatitle" <?php selected( $meta_values['sort'], 'zatitle' ); ?>><?php _e("Title Z-A",'mycontest');?></option>
							<option value="azauthor" <?php selected( $meta_values['sort'], 'azauthor' ); ?>><?php _e("Author A-Z",'mycontest');?></option>
							<option value="zaauthor" <?php selected( $meta_values['sort'], 'zaauthor' ); ?>><?php _e("Author Z-A",'mycontest');?></option>
							<option value="rand" <?php selected( $meta_values['sort'], 'rand' ); ?>><?php _e("Random",'mycontest');?></option>
						</select>
					</div>

					<div class="misc-pub-section">
						<label>
							<input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky() ); ?>>
							<?php _e( 'Stick this to the front page','mycontest' ) ?>
						</label>
					</div>

					<div class="misc-pub-section">
						<label>
							<input name="settings[votenoshow]" type="checkbox" value="1" <?php checked( $meta_values['votenoshow'] ); ?> />
							<?php _e( 'Do not show number of votes','mycontest' ) ?>
						</label>

						<br/><br/>
						<?php if(!isset($meta_values['a_entry_ribbons'])) $meta_values['a_entry_ribbons'] = 0; ?>
						<label>
							<input id="a_entry_ribbons" name="settings[a_entry_ribbons]" type="checkbox" value="1" <?php checked( $meta_values['a_entry_ribbons'] ); ?> /> 
							<?php _e( 'Show winner ribbons for top 3 entries (if enabled will change entry sort to highest votes first)','mycontest' ) ?>
						</label>
					</div>

					<?php 
						// Default values if it is not set (update)
						if(!isset($meta_values['regvoteonly'])){$meta_values['regvoteonly'] = false;}
						if(!isset($meta_values['regvoteonlyshow'])){$meta_values['regvoteonlyshow'] = false;}
						if(!isset($meta_values['regvoteonlyhtml'])){
							$meta_values['regvoteonlyhtml'] = '<a href="' . wp_login_url() . '" title="' . __('Login','mycontest') . '">' . __('You must login to vote','mycontest') . '</a>';
						}
					?>

					<div class="misc-pub-section">
						<label id="regvoteonly">
							<input name="settings[regvoteonly]" type="checkbox" value="1" <?php checked( $meta_values['regvoteonly'] ); ?> />
							<?php _e( 'Logged-in users can only vote','mycontest' ) ?>
						</label>
						<br/><br/>
						<div id="regvoteonlyhidden" style="display:none;">
							<label>
								<input name="settings[regvoteonlyshow]" type="checkbox" value="1" <?php checked( $meta_values['regvoteonlyshow'] ); ?> />
								<?php _e( 'Do not show entries to non logged-in users','mycontest' ) ?>
							</label>
							<br/><br/>
							<label for="settings[regvoteonlyhtml]"><?php _e( 'Text to display to non logged in users (HTML allowed)','mycontest'); ?><br/><?php echo __('Your login url is: ','mycontest') . wp_login_url(); ?></label>
							<textarea id="regvoteonlyhtml" name="settings[regvoteonlyhtml]" style="width:100%;max-width:100%;"><?php echo $meta_values['regvoteonlyhtml']; ?></textarea>
						</div>
					</div>



				</div>
				<!-- ####### Voting starts ####### -->
				<div class="tab-content">
					<label><?php _e( 'Voting Starts on (UTC)','mycontest'); ?></label><br/>
					<input type="text" id="s_date" name="settings[s_date]" value="<?php echo $meta_values['s_date']; ?>" size="10"  autocomplete="off">
					@ 
					<input type="text" id="s_h" name="settings[s_h]" value="<?php echo $meta_values['s_h']; ?>" size="2" maxlength="2" autocomplete="off"> : 
					<input type="text" id="s_mn" name="settings[s_mn]" value="<?php echo $meta_values['s_mn']; ?>" size="2" maxlength="2" autocomplete="off">
					<br/><br/>
					<label for="settings[s_txt]"><?php _e( 'Text to display before voting starts','mycontest'); ?></label>
					<textarea id="s_txt" name="settings[s_txt]" style="width:100%;max-width:100%;"><?php echo $meta_values['s_txt']; ?></textarea>
					<br/><br/>
					<?php if(!isset($meta_values['s_entries'])) $meta_values['s_entries'] = 0; ?>
					<label>
						<input id="s_entries" name="settings[s_entries]" type="checkbox" value="1" <?php checked( $meta_values['s_entries'] ); ?> />
						<?php _e( 'Do not show entries before voting starts','mycontest' ) ?>
					</label>
				</div>
				<!-- ####### Voting ends ####### -->
				<div class="tab-content">
					<label><?php _e( 'Voting Ends on (UTC)','mycontest');?></label><br/>
					<input type="text" id="e_date" name="settings[e_date]" value="<?php echo $meta_values['e_date']; ?>" size="10"  autocomplete="off">
					@ 
					<input type="text" id="e_h" name="settings[e_h]" value="<?php echo $meta_values['e_h']; ?>" size="2" maxlength="2" autocomplete="off"> : 
					<input type="text" id="e_mn" name="settings[e_mn]" value="<?php echo $meta_values['e_mn']; ?>" size="2" maxlength="2" autocomplete="off">
					<br/><br/>
					<label for="settings[e_txt]"><?php _e('Text to display after voting ends','mycontest'); ?></label>
					<textarea id="e_txt" name="settings[e_txt]" style="width:100%;max-width:100%;"><?php echo $meta_values['e_txt']; ?></textarea>
					<br/><br/>
					<?php if(!isset($meta_values['e_entries'])) $meta_values['e_entries'] = 0; ?>
					<label>
						<input id="e_entries" name="settings[e_entries]" type="checkbox" value="1" <?php checked( $meta_values['e_entries'] ); ?> /> 
						<?php _e( 'Do not show entries after voting ends','mycontest' ) ?>
					</label>
					<br/><br/>
					<?php if(!isset($meta_values['entry_ribbons'])) $meta_values['entry_ribbons'] = 0; ?>
					<label>
						<input id="entry_ribbons" name="settings[entry_ribbons]" type="checkbox" value="1" <?php checked( $meta_values['entry_ribbons'] ); ?> /> 
						<?php _e( 'Show winner ribbons for top 3 entries','mycontest' ) ?>
					</label>
					<br/><br/>
					<label for="e_showentries" class=""><?php _e( 'Show top','mycontest' ); ?>&nbsp;
						<input name="settings[e_showentries]" type="number" size="3" style="width:50px" value="<?php echo $meta_values['e_showentries'];?>" />
						<?php _e( ' entries after voting ends.','mycontest' ); ?>
					</label>

				</div>

				<!-- ####### Social ####### -->
				<div class="tab-content">
					<div class="misc-pub-section">
						<?php if( !in_array('curl', get_loaded_extensions()) ) echo '<div style="background-color:#DD4B39;color:#fff;padding:10px;margin-bottom:10px;">'. __('You must have cURL enabled','mycontest') . '</div>'; ?>
						<label id="socialshare">
							<?php if(!isset($meta_values['socialshare'])) $meta_values['socialshare'] = false; ?>
							<input name="settings[socialshare]" type="checkbox" value="1" <?php checked( $meta_values['socialshare'] ); ?> />
							<?php _e( 'Social Share enabled','mycontest' ) ?>
						</label>
						<br/><br/>
						<div id="socialsharehidden" style="display:none;">
							<p><?php _e('Enable/disable Social Share Services:','mycontest'); ?></p>
							<?php 
							$ssFields = array(
								'facebook' => 'Facebook',
								'twitter' => 'Twitter',
								'googleplus' => 'Google+',
								'pinterest' => 'Pinterest',
								'shortenedlink' => 'Shortened Link'
							);
							foreach($ssFields as $field => $name){
								?>
							<label id="ss<?php echo $field; ?>">
								<?php if(!isset($meta_values['ss' . $field])) $meta_values['ss' . $field] = false; ?>
								<input name="settings[ss<?php echo $field; ?>]" type="checkbox" value="1" <?php checked( $meta_values['ss' . $field] ); ?> />
								<?php echo $name; ?>
							</label>
							<br/>
								<?php
							} // end foreach fields
							?>
							<?php if(!isset($meta_values['sharedesc'])) $meta_values['sharedesc'] = ""; ?>
							<br/>
							<label for="settings[sharedesc]"><?php _e( 'Share description (html allowed)','mycontest'); ?></label>
							<textarea id="sharedesc" name="settings[sharedesc]" style="width:100%;max-width:100%;"><?php echo $meta_values['sharedesc']; ?></textarea>
							<br/><br/>
						</div>
					</div>
				</div>

				<!-- ####### Help ####### -->
				<div class="tab-content">
					<h2><?php _e('Need help?','mycontest'); ?></h2>
					
					<p><?php echo _e('Visit the <a href="http://highergroundstudio.github.io/myContest/" title="myContest Documentation">myContest Documentation</a> site.','mycontest'); ?></p>
					<hr/>
					<h2><?php _e('Have a problem?','mycontest'); ?></h2>
					<p>
					<?php echo __('Visit the <a href="https://github.com/highergroundstudio/myContest/issues" title="Issue board">myContest Issue Board</a>.', 'mycontest') .
						__('Click the New Issue button to create a new issue. You can also search for other issues and many times there may be a solution already.','mycontest') . 
						'<br/><br/>' . 
						__('Note: You must sign up for a <a href="https://github.com/signup/free" title="Github account sign up">free Github account</a> to post a new issue.','mycontest'); ?>
					</p>
					<hr/>
					<p>
						<?php echo __('The shortcode for this contest is: ','mycontest'); ?><pre><?php echo '[my_contest_shortcode id=' . $post->ID . ']'; ?></pre>
					</p>
					<hr/>
					<p>	
						<?php 
						echo 'Current PHP version: ' . phpversion(); ?>
						<br />
						<?php echo 'Your Wordpress Version is: ' . get_bloginfo ( 'version' );  ?>
						<br />
					</p>
				</div>
				<!-- ####### Debug ####### -->
				<?php if($this->debug): ?>
				<div class="tab-content">
					<p>
						<h2>Meta Array Debug</h2>
						<pre><?php print_r($meta); ?></pre>
						<h2>Settings Array Debug</h2>
						<pre><?php print_r($this->settings); ?></pre>
					</p>
				</div>
				<?php endif; // End debug if ?>
			</div>
		</div>
	<!--</div>end settings -->

</div>


<?php
function render_toolbar(){
	?>
	<div class="toolbar">
		<div class="delete-entry"><?php _e('Delete','mycontest'); ?></div>
		<div class="sortable-handle"><?php _e('Move','mycontest'); ?></div>
	</div>
	<?php
}
function field_type_input($field){
		if(empty($field['meta']) || !isset($field['meta'])) $field['meta'] = "";
		if($field['id'] == "votes"){
			if(empty($field['meta']) || !isset($field['meta'])){$field['meta'] = intval(0);}
		}
		switch ($field['type']):
			case "hidden":
				?>
				<input type="<?php echo $field['type']; ?>" name="myContest[<?php echo $field['entryid']; ?>][<?php echo $field['id']; ?>]" id="<?php echo $field['id']; ?>" value="<?php echo $field['meta']; ?>" />
				<?php
				break;
			default:
				?>
				<span class="myContest-field">
			    	<label for="<?php echo $field['id']; ?>">
			    		<?php echo $field['label']; ?>
			    	</label>
			    	<input type="<?php echo $field['type']; ?>" name="myContest[<?php echo $field['entryid']; ?>][<?php echo $field['id']; ?>]" id="<?php echo $field['id']; ?>" value="<?php echo $field['meta']; ?>" />

				</span>
				<?php
			endswitch;
	}
	function field_type_textarea($field){
		?>
		<span class="myContest-field">
			<label for="myContest[<?php echo $field['entryid']; ?>][<?php echo $field['id']; ?>]"><?php echo $field['label']; ?></label>
			<textarea name="myContest[<?php echo $field['entryid']; ?>][<?php echo $field['id']; ?>]" id="<?php echo $field['id']; ?>" ><?php echo $field['meta']; ?></textarea>
		</span>
		<?php
	}
	function field_type_image($field){
		global $pluginUrl;
		$defaultImage = $pluginUrl . 'inc/images/default-image.png';

		if (isset($field['meta']) and $field['meta'] !== '{starterImage}') { 
			//$image = aq_resize( $field['meta'], 150, 150, true );
			$image = str_replace(".jpg", "-150x150.jpg", $field['meta']);
		}else{
			$image = $defaultImage;
		}

		// If the meta field is empty then it must need to be default
		if(empty($field['meta'])) $image = $defaultImage;


		?>
			<div class="img-tools">
				<ul class="bl">
					<li><a class="button-edit ir custom_media_upload_button" href="#"><?php _e('Edit','mycontest'); ?></a></li>
					<li><a class="button-delete ir custom_clear_image_button" href="#"><?php _e('Remove','mycontest'); ?></a></li>
				</ul>
			</div>
			<img class="custom_media_image" src="<?php echo $image; ?>" height="150px" width="150px" />
			<input class='custom_default_image' type='hidden' value='<?php echo $defaultImage; ?>' />
			<input class="custom_media_url" type="hidden" name="myContest[<?php echo $field['entryid']; ?>][<?php echo $field['id']; ?>]" value="<?php echo $field['meta']; ?>" size="20" />
		<?php
	}
?>