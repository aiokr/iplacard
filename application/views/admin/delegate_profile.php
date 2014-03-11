<?php
$this->ui->html('header', '<link href="'.static_url(is_dev() ? 'static/css/flags.css' : 'static/css/flags.min.css').'" rel="stylesheet">');
$this->load->view('header');?>

<div class="page-header">
	<div class="row">
		<div class="col-md-8">
			<h1 style="position: relative;">
				<a class="thumbnail" style="width: 50px; height: 50px; position: absolute; margin-top: -2px;">
					<?php echo avatar($profile['id'], 40, 'img');?>
				</a>
				<span style="margin-left: 58px;"><?php echo $profile['name'];?></span>
			</h1>
		</div>
		<?php $this->ui->js('footer', 'nav_menu_top();
		$(window).resize(function($){
			nav_menu_top();
		});');?>
		<div class="col-md-4 menu-tabs">
			<ul class="nav nav-tabs nav-menu">
				<li class="active"><a href="#application" data-toggle="tab">个人信息</a></li>
				<li><a href="#interview" data-toggle="tab">面试审核</a></li>
				<?php if($profile['application_type'] == 'delegate' && $profile['status_code'] >= $this->delegate_model->status_code('interview_completed')) { ?><li><a href="<?php echo base_url("seat/assign/$uid");?>">席位分配</a></li><?php } ?>
			</ul>
		</div>
	</div>
</div>

<div class="menu-pills"></div>

<div class="row">
	<div class="col-md-8">
		<div class="tab-content">
			<div class="tab-pane active" id="application">
				<h3>个人信息</h3>
				<table class="table table-bordered table-striped table-hover">
					<tbody>
						<?php $rules = array(
							'name' => '姓名',
							'email' => '电子邮箱地址',
							'phone' => '电话号码',
							'application_type_text' => '申请类型',
							'status_text' => '申请状态',
						) + option('profile_list_general', array()) + option("profile_list_{$profile['application_type']}", array());
						foreach($rules as $rule => $text) { ?><tr>
							<td><?php echo $text;?></td>
							<td><?php if(!empty($profile[$rule])) echo $profile[$rule];?></td>
						</tr><?php } ?>
					</tbody>
				</table>

				<?php if($groups) { ?><h3>团队信息</h3>
				<?php if($group) { ?><table class="table table-bordered table-striped table-hover">
					<tbody>
						<tr>
							<th>代表团</th>
							<th>人数</th>
							<th>领队</th>
						</tr>
						<tr>
							<td><?php echo anchor("delegate/manage/?group={$group['id']}", $group['name']);?></td>
							<td><?php echo $group['count'];?></td>
							<td><?php if($head_delegate) { ?><span class="label label-primary">此代表是领队</span><?php }
							elseif(!$group['head_delegate']) { ?><span class="label label-warning">该团队暂无领队</span><?php }
							else echo anchor("delegate/profile/{$group['head_delegate']['id']}", icon('user', false).$group['head_delegate']['name']);?></td>
						</tr>
					</tbody>
				</table>
				
				<div class="btn-group">
					<button type="button" data-toggle="modal" data-target="#group_edit" onclick="$('input[name=head_delegate]').attr('checked', <?php echo set_value('group', $head_delegate ? true : false) ? 'true' : 'false';?>); $('select[name=group]').removeAttr('disabled');" class="btn btn-primary"><?php echo icon('retweet');?>调整团队</button>
					<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<?php if(!$head_delegate) { ?><li><a data-toggle="modal" data-target="#group_edit" onclick="$('input[name=head_delegate]').attr('checked', true); $('select[name=group]').attr('disabled', true);">设为团队领队</a></li><?php }
						else { ?><li><a data-toggle="modal" data-target="#group_edit" onclick="$('input[name=head_delegate]').attr('checked', false); $('select[name=group]').attr('disabled', true);">取消领队属性</a></li><?php } ?>
						<li><a data-toggle="modal" data-target="#group_remove">取消团队</a></li>
					</ul>
                </div>
				
				<?php echo form_open("delegate/group/remove/{$profile['id']}", array(
					'class' => 'modal fade form-horizontal',
					'id' => 'group_remove',
					'tabindex' => '-1',
					'role' => 'dialog',
					'aria-labelledby' => 'remove_label',
					'aria-hidden' => 'true'
				));?><div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<?php echo form_button(array(
									'content' => '&times;',
									'class' => 'close',
									'type' => 'button',
									'data-dismiss' => 'modal',
									'aria-hidden' => 'true'
								));?>
								<h4 class="modal-title" id="remove_label">转换为个人代表</h4>
							</div>
							<div class="modal-body">
								<p>将会转换<?php echo icon('user', false).$profile['name'];?>为个人代表。</p>
								
								<div class="form-group <?php if(form_has_error('confirm')) echo 'has-error';?>">
									<?php echo form_label('确认转换', 'confirm', array('class' => 'col-lg-3 control-label'));?>
									<div class="col-lg-9">
										<div class="checkbox">
											<label>
												<?php echo form_checkbox(array(
													'name' => 'confirm',
													'id' => 'confirm',
													'value' => true,
													'checked' => false,
												)); ?> 确认转换为个人代表
											</label>
										</div>
										<?php echo form_error('confirm');?>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<?php echo form_button(array(
									'content' => '取消',
									'type' => 'button',
									'class' => 'btn btn-link',
									'data-dismiss' => 'modal'
								));
								echo form_button(array(
									'name' => 'submit',
									'content' => '确认转换',
									'type' => 'submit',
									'class' => 'btn btn-primary',
									'onclick' => 'loader(this);'
								)); ?>
							</div>
						</div>
					</div>
				<?php echo form_close(); } else { ?><p>此申请者为个人申请代表，不属于任何团队。</p>
				<a data-toggle="modal" data-target="#group_edit" class="btn btn-primary"><?php echo icon('retweet');?>转换为团队代表</a><?php }
				echo form_open("delegate/group/edit/{$profile['id']}", array(
					'class' => 'modal fade form-horizontal',
					'id' => 'group_edit',
					'tabindex' => '-1',
					'role' => 'dialog',
					'aria-labelledby' => 'edit_label',
					'aria-hidden' => 'true'
				));?><div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<?php echo form_button(array(
									'content' => '&times;',
									'class' => 'close',
									'type' => 'button',
									'data-dismiss' => 'modal',
									'aria-hidden' => 'true'
								));?>
								<h4 class="modal-title" id="edit_label"><?php echo $group ? '调整团队' : '加入团队';?></h4>
							</div>
							<div class="modal-body">
								<p>将会调整<?php echo icon('user', false).$profile['name'];?>的团队属性，调整完成后将会通知代表。</p>
								
								<div class="form-group <?php if(form_has_error('group')) echo 'has-error';?>">
									<?php echo form_label('所属团队', 'group', array('class' => 'col-lg-3 control-label'));?>
									<div class="col-lg-5">
										<?php echo form_dropdown('group', array('' => '选择代表团...') + $groups, set_value('group', $group ? $group['id'] : ''), 'class="form-control" id="committee"');
										if(form_has_error('group'))
											echo form_error('group');
										else { ?><div class="help-block">代表所属的代表团。</div><?php } ?>
									</div>
								</div>
								
								<div class="form-group <?php if(form_has_error('head_delegate')) echo 'has-error';?>">
									<?php echo form_label('设为领队', 'head_delegate', array('class' => 'col-lg-3 control-label'));?>
									<div class="col-lg-9">
										<div class="checkbox">
											<label>
												<?php echo form_checkbox(array(
													'name' => 'head_delegate',
													'id' => 'head_delegate',
													'value' => true,
													'checked' => set_value('group', $head_delegate ? true : false),
												)); ?> 设置此代表为团队领队
											</label>
										</div>
										<?php if(form_has_error('head_delegate'))
											echo form_error('head_delegate');
										else { ?><div class="help-block">如设置的代表团已存在领队，此操作仍然有效。</div><?php } ?>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<?php echo form_button(array(
									'content' => '取消',
									'type' => 'button',
									'class' => 'btn btn-link',
									'data-dismiss' => 'modal'
								));
								echo form_button(array(
									'name' => 'submit',
									'content' => '确认提交',
									'type' => 'submit',
									'class' => 'btn btn-primary',
									'onclick' => '$(\'select[name=group]\').removeAttr(\'disabled\'); loader(this);'
								)); ?>
							</div>
						</div>
					</div>
				<?php echo form_close(); } ?>
				
				<?php if(!empty($profile['experience'])) { ?><h3>参会经历</h3>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<?php $rules = option('profile_list_experience');
						foreach($rules as $rule => $text) { ?><th><?php echo $text;?></th><?php } ?>
					</thead>
					<tbody>
						<?php foreach($profile['experience'] as $experience) { ?>
						<tr><?php foreach($rules as $rule => $text) { ?>
							<td><?php echo $experience[$rule];?></td><?php } ?>
						</tr><?php } ?>
					</tbody>
				</table><?php } ?>

				<?php if(!empty($profile['club'])) { ?><h3>社会活动</h3>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<?php $rules = option('profile_list_club');
						foreach($rules as $rule => $text) { ?><th><?php echo $text;?></th><?php } ?>
					</thead>
					<tbody>
						<?php foreach($profile['club'] as $experience) { ?>
						<tr><?php foreach($rules as $rule => $text) { ?>
							<td><?php echo $experience[$rule];?></td><?php } ?>
						</tr><?php } ?>
					</tbody>
				</table><?php } ?>

				<?php if(!empty($profile['test'])) { ?><h3 id="test">学术测试</h3>
				<table class="table table-bordered table-striped table-hover">
					<tbody>
						<?php $questions = option('profile_list_test');
						foreach($questions as $qid => $question) { ?>
						<tr><td><?php echo $question;?></td></tr>
						<tr><td><?php echo nl2br($profile['test'][$qid]);?></td></tr><?php } ?>
					</tbody>
				</table><?php } ?>
			</div>
			
			<div class="tab-pane" id="interview">
				<?php if(isset($seat) && !empty($seat)) { ?>
				<h3>席位分配</h3>
				<table class="table table-bordered table-striped table-hover flags-16">
					<tbody>
						<tr>
							<td>席位名称</td>
							<td><?php echo flag($seat['iso']).$seat['name'];?></td>
						</tr>
						<tr>
							<td>委员会</td>
							<td><?php echo "{$committee['name']}（{$committee['abbr']}）";?></td>
						</tr>
						<tr>
							<td>分配时间</td>
							<td><?php echo sprintf('%1$s（%2$s）', date('n月j日 H:i:s', $seat['time']), nicetime($seat['time']));?></td>
						</tr>
						<tr>
							<td>状态</td>
							<td><?php echo $profile['status_code'] > $this->delegate_model->status_code('seat_assigned') ? '已经确认锁定' : '尚未确认锁定';?></td>
						</tr>
					</tbody>
				</table>
				
				<hr /><?php } ?>
				
				<?php if(!empty($interviews)) {
				foreach($interviews as $interview) { ?><h3><?php echo $interview['id'] == $current_interview ? '当前面试信息' : '早前面试信息';?></h3>
				<table class="table table-bordered table-striped table-hover">
					<tbody>
						<tr>
							<td>状态</td>
							<td><?php echo $interview['status_text'];?></td>
						</tr>
						<tr>
							<td>指派面试官</td>
							<td><?php echo anchor("user/edit/{$interview['interviewer']['id']}", icon('user').$interview['interviewer']['name']);
							if(!empty($interview['interviewer']['committee']))
								echo "（{$interview['interviewer']['committee']['name']}）";?></td>
						</tr>
						<tr>
							<td>指派时间</td>
							<td><?php echo sprintf('%1$s（%2$s）', date('n月j日 H:i:s', $interview['assign_time']), nicetime($interview['assign_time']));?></td>
						</tr>
						<?php if(!empty($interview['schedule_time'])) { ?><tr>
							<td>安排时间</td>
							<td><?php echo sprintf('%1$s（%2$s）', date('n月j日 H:i:s', $interview['schedule_time']), nicetime($interview['schedule_time']));?></td>
						</tr><?php }
						if(!empty($interview['finish_time'])) { ?><tr>
							<td><?php echo $interview['status'] == 'cancelled' ? '取消时间' : '完成时间';?></td>
							<td><?php echo sprintf('%1$s（%2$s）', date('n月j日 H:i:s', $interview['finish_time']), nicetime($interview['finish_time']));?></td>
						</tr><?php }
						if(!empty($interview['score'])) { ?><tr>
							<td>面试总分</td>
							<td><strong><?php echo round($interview['score'], 2);?></strong></td>
						</tr><?php }
						if(!empty($interview['feedback']['score'])) { ?><tr>
							<td>详细评分</td>
							<td><?php foreach(option('interview_score_standard', array('score' => array('name' => '总分'))) as $sid => $one)
							{
								echo "<span class=\"label label-primary\">{$one['name']}</span> {$interview['feedback']['score'][$sid]} ";
							} ?></td>
						</tr><?php }
						if(!empty($interview['feedback'])) { ?><tr>
							<td style="min-width: 100px;">面试反馈</td>
							<td><?php echo $interview['feedback']['feedback'];?></td>
						</tr><?php } ?>
					</tbody>
				</table>
				<hr />
				<?php } } ?>

				<h3>事件日志</h3>
				<?php //TODO ?>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div id="operation_bar">
			<h3 id="operation">操作</h3>
			<div id="operation_area">
				操作载入中……
			</div>
			
			<hr />
		</div>
		
		<div id="note_bar">
			<h3 id="note">笔记</h3>
			<div id="note_area">
				操作载入中……
			</div>
		</div>
	</div>
</div>

<?php
$ajax_url = base_url("delegate/ajax/sidebar?id=$uid");
$operation_js = <<<EOT
$.ajax({
	url: "$ajax_url",
	dataType : "json",
	success : function( sidebar ){
		$("#operation_bar").html( sidebar.html );
	}
});
EOT;
$this->ui->js('footer', $operation_js);

$note_url = base_url("delegate/ajax/note?id=$uid");
$note_js = <<<EOT
$.ajax({
	url: "$note_url",
	dataType : "json",
	success : function( note ){
		$("#note_area").html( note.html );
	}
});
EOT;
$this->ui->js('footer', $note_js);

$this->load->view('footer');?>