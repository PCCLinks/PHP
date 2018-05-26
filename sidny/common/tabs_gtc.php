<?php ?>
<div id="tabs-gtc" style="width: 845px; font-size: 10pt">
    <div id='gtc-program'>
        <ul>
            <li><a href="tabs/gtc_orientation.php">Orientation</a></li>
            <li><a href="tabs/gtc_eval_interview.php">Evaluation/Interview</a></li>
            <li><a href="tabs/contact_riskfactor.php">Risk Factors</a></li>
            <li><a href="tabs/contact_career.php">Careers</a></li>
           <!-- <li><a href="tabs/gtc_hs.php">HS Info</a></li> -->
            <li><a href="tabs/gtc_placement.php">Campus Preference</a></li>
           <!--- <li><a href="tabs/gtc_cohort.php">Cohort/Trans/Perform</a></li>  -->
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(function() {
            $( "#tabs-gtc" ).tabs({
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