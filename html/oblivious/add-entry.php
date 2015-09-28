

        <div class="main-container oblivious-add">
            <div class="main wrapper clearfix">
			 
                <article id="oblivious_addentry">
                <section class="measure p2" id="">
                <h3>Have an invite?</h3>
                <p style="display:none;" id="invite-status">Successfully processed invite! <a class="btn--red dismiss-button">dismiss</a></p>
                <hr />
                	<form id="inviteform" action="#" role="form" class="my2">
                		<label for="inviteinput">Enter Invite</label>
                		<input type="text" id="encodedinvite" class="full-width" />
<!--                 		<input id="inviteinput" type=file accept="image/*;capture=camera">				      	 -->
                		<input id="submit-invite-button" value="Submit Invite" class="btn--blue full-width" type="button">
                	</form>
                	
                </section>
                 <section class="measure p2" id="">
					      <h3>Create Entry</h3>
						<hr />
					
					      <form id="addentryform" action="#" role="form" class="my2">
					      	<img class="add-image-button" src="<?php echo $path_from_index;?>flaticon/svg/picture56.svg" alt="oblivious entry"> 
					      	<input id="addimage-input" type=file accept="image/*;capture=camera">				      	
					      	<hr />
					        <label class="clientsidecrypto" for="crypto">Client-Side Crypto</label>
					        <input rv-on-change="viewdata.togglecrypto" class="clientsidecrypto" name="crypto" type="checkbox" />
					        
					        <label for="password">Password</label>
					        <input 	rv-on-change="viewdata.setAddProperty" id="entry-password" name="password" placeholder="Enter password" type="password">
					        <label for="msg">Message</label>
					        <textarea 	rv-on-change="viewdata.setAddProperty" id="entry-text" name="msg" rows="3" placeholder="Entry contents..."></textarea>
					        <label for="expiration">Expiration</label>
					        <select	rv-on-change="viewdata.setAddProperty" name="expiration" id="entry-expiration">
					        	<option value="never">Never</option>
					        	<option value="burnafter">Burn After Reading</option>
					        	<option value="5min">5 minutes</option>
					        	<option value="10min">10 minutes</option>
					        	<option value="1hour">1 hour</option>
					        	<option value="1day">1 day</option>
					        	<option value="1week">1 week</option>
					        	<option value="1month">1 month</option>
					        	<option value="1year">1 year</option>
					        </select>
					        <label for="metadata">Meta-Data</label>
					        <input id="metadata_entry" type="text" name="metadata" class="full-width" />
					        <textarea id="metadata_preview" disabled=true rows=3 placeholder="Entry MetaData JSON"></textarea>
					        <label for="category">
					        	Choose a Category
					        </label>
					        <select	rv-on-change="viewdata.setAddProperty" id="addentry-category">
                	    		<option class="category-btn btn btn--s full-width">(choose a category)</option>
                	    		<option rv-each-category="viewdata.categories" class="category-btn btn btn--s full-width">{category}</option>
                	    	</select>
                	    	<hr />
					        <input id="submit-entry-button" value="Submit Entry" class="btn--blue" type="button">
					      </form>
					    </section>   
                </article>
               
                

            </div> <!-- #main -->
        </div> <!-- #main-container -->
