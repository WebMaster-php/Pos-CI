        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php base_url();?>profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
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
                                        <?php if($this->input->get('kb_q')){?>
											<div class="m-t-30 text-center">
												<h4>Search Results For "<?php echo $this->input->get('kb_q'); ?>"</h4>
											</div>
										<?php } else { ?>
											<div class="m-t-30 text-center">
												<h4>Search Our Knowledge Base</h4>
											</div>
										<?php } ?>
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
									<?php if(count($groups) == 0){ ?>
									<p class="no-margin"><?php echo _l('clients_knowledge_base_articles_not_found'); ?></p>
									<?php } ?>
									<?php if(!$this->input->get('groupid') && !$this->input->get('kb_q')){ ?>
									<?php foreach($groups as $group){ ?>
									<div class="col-md-12">
										<div class="article_group_wrapper">
											<h4 class="bold"><i class="fa fa-folder-o"></i> <a href="<?php echo site_url('knowledge-base'); ?>?groupid=<?php echo $group['groupid']; ?>"><?php echo $group['name']; ?></a>
												<small><?php echo count($group['articles']); ?></small>
											</h4>
											<p><?php echo $group['description']; ?></p>
										</div>
									</div>
									<?php } ?>
									<?php do_action('after_kb_groups_customers_area'); ?>
									<?php } else { ?>
									<div class="col-md-12">
										<?php foreach($groups as $group){ ?>
										<h4 class="bold mbot30"><i class="fa fa-folder-o"></i> <?php echo $group['name']; ?></h4>
										<ul class="list-unstyled articles_list">
											<?php foreach($group['articles'] as $article) { ?>
											<li>
												<a href="<?php echo site_url('knowledge-base/'.$article['slug']); ?>" class="article-heading"><?php echo $article['subject']; ?></a>
												<div class="text-muted mtop10"><?php echo strip_tags(mb_substr($article['description'],0,250)); ?>...</div>
											</li>
											<hr />
											<?php } ?>
										</ul>
										<?php } ?>
									</div>
									<?php do_action('after_kb_group_customers_area'); ?>
									<?php } ?>
								</div>
							</div>
							<div>
                        </div>
                    </div>
                </div>


            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->