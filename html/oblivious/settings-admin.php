

        <div class="main-container oblivious-settings">
            <div class="main wrapper clearfix">
			 	<article id="blackbook_management">
			 		<section class="measure p2">
			 			<h3>Blackbook</h3>
			 			<a id="clear-all-blackbook-button" data-cleartype="all" class="category-btn btn btn--red full-width">
               				Clear Blackbook
               			</a>
               			
			 		</section>
			 	</article>
                <article id="oblivious_addcategory">
                 <section class="measure p2" id="forms">
					      <h3>Create Category</h3>
					
					      
					      <form id="addentryform" action="#" role="form" class="my2">
					        <label for="category">
					        	Category Name
					        </label>
					        <input id="new-category" type="text" class="full-width" />
					        <hr />
					        <input id="submit-category-button" value="Submit Category" class="btn--blue" type="button">
					      </form>
					    </section>   
                </article>
               	<article id="oblivious_categorylist">
               		<section class="measure p2">
               			<div id="">
                	    	<select  id="current-category">
                	    		<option class="category-btn btn btn--s full-width">(choose a category)</option>
                	    		<option rv-each-category="viewdata.categories" class="category-btn btn btn--s full-width">{category}</option>
                	    	</select>
                		</div>
                		<a id="remove-category-button" class="category-btn btn btn--red full-width">
               				REMOVE
               			</a>
               			<hr />
               			<a class="category-btn btn btn--s full-width" rv-each-category="viewdata.categories">
               				{category}
               			</a>
               		</section>
               	</article>
                

            </div> <!-- #main -->
        </div> <!-- #main-container -->
