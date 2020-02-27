        
		<div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Knowledgebase</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Search Results</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="search-result-box m-t-30 card-box">
							<?php if(count($groups) > 0 || $this->input->get('kb_q')){ ?>
                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <div class="pt-3 pb-4">
										<?php echo form_open(site_url('clients/knowledge_base'),array('method'=>'GET')); ?>
                                        <div class="input-group m-t-10">
                                            <input type="search" name="kb_q" placeholder="<?php echo _l('have_a_question'); ?>" class="form-control" value="<?php echo $this->input->get('kb_q'); ?>">
                                        <span class="input-group-btn">
                                            <button style="height: 39px;' type="submit" class="btn waves-effect waves-light btn-custom"><i class="fa fa-search m-r-5"></i> <?php echo _l('kb_search'); ?></button>
                                        </span>
                                        </div>
										<?php echo form_close(); ?>
                                    </div>
                                </div>
                            </div>
							<?php } ?>
                            <!-- end row -->
							
							
                            <ul class="nav nav-tabs tabs-bordered">
                                <li class="nav-item">
                                    <a href="#home" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                        All results <span class="badge badge-success ml-1"></span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
									<div class="panel_s">
										<div class="panel-body">
											<div class="row kb-article">
												<div class="col-md-<?php if(count($related_articles) == 0){echo '12';}else{echo '8';} ?>">
													<h1 class="bold no-mtop kb-article-single-heading"><?php echo $article->subject; ?></h1>
													<hr class="no-mtop" />
													<div class="mtop10 tc-content">
														<?php echo $article->description; ?>
													</div>
													<hr />
													<h4 class="mtop20"><?php echo _l('clients_knowledge_base_find_useful'); ?></h4>
													<div class="answer_response"></div>
													<div class="btn-group mtop15 article_useful_buttons" role="group">
														<input type="hidden" name="articleid" value="<?php echo $article->articleid; ?>">
														<button type="button" data-answer="1" class="btn btn-success"><?php echo _l('clients_knowledge_base_find_useful_yes'); ?></button>
														<button type="button" data-answer="0" class="btn btn-danger"><?php echo _l('clients_knowledge_base_find_useful_no'); ?></button>
													</div>
												</div>
												<?php if(count($related_articles) > 0){ ?>
												<div class="visible-xs visible-sm">
													<br />
												</div>
												<div class="col-md-4">
													<h4 class="bold no-mtop h3 kb-related-heading"><?php echo _l('related_knowledgebase_articles'); ?></h4>
													<hr class="no-mtop" />
													<ul class="mtop10 articles_list">
														<?php foreach($related_articles as $relatedArticle) { ?>
														<li>
															<a href="<?php echo site_url('knowledge_base/'.$relatedArticle['slug']); ?>" class="article-heading"><?php echo $relatedArticle['subject']; ?></a>
															<div class="text-muted mtop10"><?php echo mb_substr(strip_tags($relatedArticle['description']),0,150); ?>...</div>
														</li>
														<hr />
														<?php } ?>
													</ul>
												</div>
												<?php }	?>
												<?php do_action('after_single_knowledge_base_article_customers_area',$article->articleid); ?>
											</div>
										</div>
									</div>
							</div>
						</div>
                    </div>
                </div>


            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
		<script>
    $(function(){
		
       $('.article_useful_buttons button').on('click', function(e) {
		   e.preventDefault();
           var data = {};
           data.answer = $(this).data('answer');
           data.articleid = '<?php echo $article->articleid; ?>';
           $.post('<?php echo base_url();?>clients/add_kb_answer', data).done(function(response) {
               response = JSON.parse(response);
               if (response.success == true) {
                   $(this).focusout();
               }
               $('.answer_response').html(response.message);
           });
       });
   });
</script>