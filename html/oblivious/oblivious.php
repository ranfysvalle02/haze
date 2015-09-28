

        <div class="main-container oblivious-home remodal-bg">
            <div class="main wrapper clearfix">
			 <div id="subnav-home">
			 		<article>
			 			<section>
			 				<h3>Blackbook</h3>
			 				<p>                 		<a id="loadblackbook-button" class="btn--blue full-width">Load Blackbook</a>
			 				</p>
			 			</section>
			 		</article>
			 		<article>
			 			<section>
			 			<div rv-if="oblivious_data.subnav.categories">
	  						
	                	    <div id="category-container">
	                	    	<select  rv-on-change="oblivious_data.subnav.travel"	 id="current-category">
	                	    		<option class="category-btn btn btn--s full-width">(choose a category)</option>
	                	    		<option rv-each-category="oblivious_data.subnav.content" class="category-btn btn btn--s full-width">{category}</option>
	                	    	</select>
	                		</div>
	                		<hr />
	                		
	                	</div>
			 			</section>
			 		</article>
	  					
                	                  
                </div>
                <article id="oblivious_entrylist">
                    <section rv-each-entry="oblivious_data.entries">
                    <div rv-id="entry.entryid">
                    	<div class="entry-icon">
                    		<img src="<?php echo $path_from_index;?>flaticon/svg/lettere.svg" alt="oblivious entry">
                    	</div>
                    	<div class="">
                    		<h6 class="">{entry.entryid}</h6>
                    		<span  class="icon-holder">
                    				<img rv-if="entry.meta.burnafterreading" src="<?php echo $path_from_index;?>flaticon/svg/fire14.svg" alt="oblivious entry">
                    		
                    				<img rv-if="entry.meta.containsimage" src="<?php echo $path_from_index;?>flaticon/svg/picture56.svg" alt="oblivious entry">
                    				<img rv-if="entry.meta.opendiscussion" src="<?php echo $path_from_index;?>flaticon/svg/chat78.svg" alt="oblivious entry"> 
                    				<img rv-if="entry.meta.encrypted" src="<?php echo $path_from_index;?>flaticon/svg/locked43.svg" alt="oblivious entry">
                    				<img rv-if="entry.meta.krypi" src="<?php echo $path_from_index;?>flaticon/svg/login13.svg" alt="oblivious entry">			
									<img rv-if="entry.meta.unencrypted" src="<?php echo $path_from_index;?>flaticon/svg/open99.svg" alt="oblivious entry">
									  <a rv-eid="entry.entryid" href="#modal" rv-if="entry.meta.opendiscussion"  id="" class="oblivious-entry-button add-comment-button btn--blue btn--s">Add Comment</a>
				                	<a rv-eid="entry.entryid" rv-ecat="entry.category" href="#invitemodal" id="" class="invite-button btn btn--s  full-width">Invite</a>
									<a rv-eid="entry.entryid" class="oblivious-entry-button btn btn--s btn--green full-width">View</a>
                    			</span>
                    	</div>
                    </div>
                    </section>
                </article>
                <article style="display:none;" id="oblivious_viewentry">
                	<section>
                	
                	<a id="return-to-entries" class="category-btn btn btn--s full-width">Back to Entries</a>
                    <div class="oblivious-view-entry">
                    	<div rv-each-entry="oblivious_viewentry_data.entries" class="">
                    		<h6 class="">{entry.category}:{entry.entryid}</h6>
                    		<textarea id="view-entry-meta" class="view-entry-meta full-width" disabled="disabled" rows=10>
                    		
                    		</textarea>
                    		<span  class="icon-holder">
                    				<img rv-if="entry.meta.containsimage" src="<?php echo $path_from_index;?>flaticon/svg/picture56.svg" alt="oblivious entry">
                    				<img rv-if="entry.meta.burnafterreading" src="<?php echo $path_from_index;?>flaticon/svg/fire14.svg" alt="oblivious entry">
                    				
                    				<img rv-if="entry.meta.opendiscussion" src="<?php echo $path_from_index;?>flaticon/svg/chat78.svg" alt="oblivious entry"> 
                    				<img rv-if="entry.meta.encrypted" src="<?php echo $path_from_index;?>flaticon/svg/locked43.svg" alt="oblivious entry">
                    				<img rv-if="entry.meta.krypi" src="<?php echo $path_from_index;?>flaticon/svg/login13.svg" alt="oblivious entry">			
									<img rv-if="entry.meta.unencrypted" src="<?php echo $path_from_index;?>flaticon/svg/open99.svg" alt="oblivious entry">
									<a rv-eid="entry.entryid" href="#modal" rv-if="entry.meta.opendiscussion"  id="" class="oblivious-entry-button add-comment-button btn--blue btn--s">Add Comment</a>
				                	<a rv-eid="entry.entryid" rv-ecat="entry.category" href="#invitemodal" id="" class="invite-button btn btn--s  full-width">Invite</a>
									
                    		</span>
                    	</div>
                    	<div class="processed-entry-contents">
                    		<div rv-each-econtent="oblivious_viewentry_data.contents" class="entry-content-container">
                    			<a  rv-if="econtent.image" rv-href="econtent.image" download="oblivious.png">
                    				<img rv-if="econtent.image" rv-src="econtent.image"  class="processed-entry-image" />
                    			</a>
                    			<hr />
                    			<p rv-if="econtent.text">{econtent.text}</p>
                    			<p rv-if="econtent.createdate">{econtent.createdate}</p>
                    			<p rv-if="econtent.expiretime">{econtent.expiretime}</p>
                    			<a  rv-if="econtent.identicon" rv-href="econtent.identicon" rv-download="entry.entryid">
                    				<img rv-if="econtent.identicon" rv-src="econtent.identicon"  class="processed-entry-identicon" />
                    			</a>
                    		</div>
                    	</div>
                    </div>
                    </section>
                </article>

               
                

            </div> <!-- #main -->
        </div> <!-- #main-container -->
        <div class="remodal" data-remodal-id="modal">
		  <button data-remodal-action="close" class="remodal-close"></button>
		  <div class="modal-content">
		  	<h3>Add Comment</h3>
						<hr />
					
					      <form id="addcommentform" action="#" role="form" class="my2">
					      	<img class="add-image-button" src="<?php echo $path_from_index;?>flaticon/svg/picture56.svg" alt="oblivious entry"> 
					      	<input id="addimage-input" type=file accept="image/*;capture=camera">				      	
					      	<hr /><label for="msg">Message</label>
					        <textarea 	rv-on-change="" id="comment-text" name="msg" rows="3" placeholder="New comment..."></textarea>
					        
					      </form>
		  </div>
		  <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
		  <button data-remodal-action="confirm" class="remodal-confirm" id="send-comment-button">Send</button>
		</div>
		<div class="remodal" data-remodal-id="invitemodal">
		  <button data-remodal-action="close" class="remodal-close"></button>
		  <div class="modal-content">
		  	<h3>Generate Invite</h3>
						<hr />
							<label for="invite-pwd">Password*:</label>
							<input id="invite-pwd" type="text" placeholder="(Required)" class="full-width" />
					      <input type="text" class="full-width" readonly="readonly" id="encodedinvite" placeholder="encodedinvite" />
					      <button id="send-invite-button" class="btn--green full-width">Generate Invite</button>
					      
		  </div>
		  <button data-remodal-action="cancel" class="remodal-cancel full-width">Close</button>
		</div>
