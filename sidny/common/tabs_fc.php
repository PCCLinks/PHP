<?php ?>
<div id="tabs-fc" style="width: 845px; font-size: 10pt">
    <div id='fc-program'>
    <ul>
      <li><a href="tabs/fc_application.php">Application</a></li>
        <li><a href="tabs/fc_credits.php">Classes</a></li>
        <li><a href="tabs/fc_funds.php">Scholarship Funds</a></li>
        <li><a href="tabs/fc_plan.php">Plans</a></li>
    
    </div>
</div>
<script type="text/javascript">
    $(function() {
            $( "#tabs-fc" ).tabs({
                    ajaxOptions: {
                            error: function( xhr, status, index, anchor ) {
                                    $( anchor.hash ).html(
                                            "Couldn't load this tab. We'll try to fix this as soon as possible. " +
                                            "If this wouldn't be a demo." );
                            }
                    }
            });
    });
</script>