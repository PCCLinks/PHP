<?php ?>
<div id="tabs-contact" style="width: 845px; font-size: 10pt">
    <div id='cotacts'>
    <ul>
        <li><a href="tabs/contact.php">Contact</a></li>
        <li><a href="tabs/banner_contact.php">Banner</a></li>
    </ul>
    </div>
</div>
<script type="text/javascript">
    $(function() {
            $( "#tabs-contact" ).tabs({
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