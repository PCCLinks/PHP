<?php ?>
<div id="tabs-pd" style="width: 845px; font-size: 10pt">
    <div id='pd-program'><ul>
            <li><a href="tabs/pd_other.php">Other</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(function() {
            $( "#tabs-pd" ).tabs({
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