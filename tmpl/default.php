<?php
// no direct access
defined('_JEXEC') or die;
?>
<style type="text/css">
  .cf-dev {
    float: left;
    margin-right: 20px;
  }
  .cf-status-on {
    color: #4CAE4C;
    font-weight: bold;
  }
  .cf-status-off {
    color: #D43F3A;
    font-weight: bold;
  }
</style>
<script>
  window.addEvent('domready', function() {
    getCFStatus();
  });

  var cfStatus = 0;

  function getCFStatus() {
    var result = new Request.JSON({
      method: "get",
      url: "modules/mod_cloudflaredev/ajax.php?task=status",
      onSuccess: function(data){
        if(data.result != 'success') {
          $('cf-dev-error').set('html', data.msg);
        } else {
          var status = data.response.result.objs[0].dev_mode;
          var now = Math.round(new Date().getTime() / 1000);
          if(status > 0 && status < now) {
            status = 0;
          }
          cfStatus = status;
          $('cf-dev-change').set('disabled', false);
          if(status) {
            $('cf-dev-status')
              .set('html', '<?php echo JText::_('JON'); ?>')
              .removeClass('cf-status-off')
              .addClass('cf-status-on');
          } else {
            $('cf-dev-status')
              .set('html', '<?php echo JText::_('JOFF'); ?>')
              .removeClass('cf-status-on')
              .addClass('cf-status-off');
          }
        }

      }
    }).get();
  }

  function changeCFStatus() {
    var mode = (cfStatus) ? '0' : '1';
    var result = new Request.JSON({
      method: "get",
      url: "modules/mod_cloudflaredev/ajax.php?task=change&mode=" + mode,
      onSuccess: function(data){
        if(data.result != 'success') {
          $('cf-dev-error').set('html', data.msg);
        } else {
          var status = (cfStatus) ? 0 : 1;
          cfStatus = status;
          if(status) {
            $('cf-dev-status')
              .set('html', '<?php echo JText::_('JON'); ?>')
              .removeClass('cf-status-off')
              .addClass('cf-status-on');
          } else {
            $('cf-dev-status')
              .set('html', '<?php echo JText::_('JOFF'); ?>')
              .removeClass('cf-status-on')
              .addClass('cf-status-off');
          }
        }

      }
    }).get();
  }
</script>
<div class="cf-dev" class="<?php echo $moduleclass_sfx ?>">
  <?php echo JText::_('MOD_CLOUDFLAREDEV_DEV_MODE'); ?>: <span id="cf-dev-status"></span> <button disabled="disabled" id="cf-dev-change" onclick="changeCFStatus(); return false;"><?php echo JText::_('MOD_CLOUDFLAREDEV_TOGGLE_STATUS'); ?></button> <span id="cf-dev-error" class="cf-status-off"></span>
</div>
