<?php
/*
 * Cru Doctrine
 * Modules - Module Home
 * Campus Crusade for Christ
 */
?>


<div class="register_banner" style="background: url(<?php echo '../'.$module['FrontImg']; ?>) no-repeat;">
<div class="container ">
<div class="rgstr_bnr_txt">
<h1>Module <?php echo number_format($module['Number'], 0); ?>: <?php echo $module['Name']; ?></h1>
</div>
</div>
</div>

<div class="container main">
<div class="row">
<div class="col-md-12">

 <div class="row">
 <div class="col-md-12">
 <p><?php echo $module['Descr']; ?></p>
         <a href="?s=<?php echo $module['FirstSection']; ?>" class="ui-state-default ui-corner-all shadow-medium">Continue<span class="ui-icon ui-icon-circle-triangle-e"></span></a>
 </div>
 </div>
</div>
</div>
</div>
