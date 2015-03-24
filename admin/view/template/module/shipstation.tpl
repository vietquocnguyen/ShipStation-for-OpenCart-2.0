<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a href="<?php echo $keygen; ?>" class="button" id="keygen"><?php echo $button_keygen; ?></a><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
  </div>
  <div class="shipstation">
    <div class="vtabs">
      <a href="#tab-general"><?php echo $tab_general; ?></a>
      <a href="#tab-error"><?php echo $tab_error; ?></a>
    </div>
    <div id="tab-general" class="vtabs-content">
      <div class="heading"><?php echo $heading_general; ?></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tbody>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="shipstation_status">
                  <?php if ($shipstation_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_config_key; ?></td>
              <td><input type="text" name="shipstation_config_key" id="shipstation_config_key" value="<?php echo $shipstation_config_key; ?>" size="60" />
                <?php if ($error_config_key) { ?>
                <span class="error"><?php echo $error_config_key; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_config_ver_key; ?></td>
              <td><input type="text" name="shipstation_verify_key" id="shipstation_verify_key" value="<?php echo $shipstation_verify_key; ?>" size="60" />
                <?php if ($error_verify_key) { ?>
                <span class="error"><?php echo $error_verify_key; ?></span>
                <?php } ?></td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>
    <div id="tab-error" class="vtabs-content">
      <div class="heading"><?php echo $heading_error; ?></div>
      <div class="content">
        <textarea wrap="off" style="width: 98%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"><?php echo $log; ?></textarea>
      </div>
      <div style="padding: 10px;"><a onclick="location = '<?php echo $clear; ?>';" class="button"><span><?php echo $button_clear; ?></span></a></div>
    </div>
    <div style="clear: both;"></div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function(){
	// Confirm Key Generation
	$('#keygen').click(function(){
		if ($(this).attr('href') != null && $(this).attr('href').indexOf('keygen', 1) != -1) {
			if (!confirm('<?php echo $text_confirm; ?>')) {
				return false;
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
<?php echo $footer; ?>
