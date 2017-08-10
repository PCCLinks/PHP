<?php ?>
<div id="tabs-map" style="width: 845px; font-size: 10pt">
    <div id='map-program'>
    <ul>
      <li><a href="tabs/map_application.php">Application</a></li>
        <li><a href="tabs/map_classes.php">Classes</a></li>
        <li><a href="tabs/map_elpa.php">ELPA</a></li>
        <li><a href="tabs/map_ged.php">GED</a></li>
        <!--<li><a href="tabs/map_plan.php">Plan</a></li>-->
    
    </div>
</div>
<script type="text/javascript">
    $(function() {
            $( "#tabs-map" ).tabs({
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