<?php ?>
<div id="tabs-contact" style="width: 845px; font-size: 10pt">
    <div id='cotacts'>
    <ul>
        <li><a href="tabs/student_demographics.php">PCC Prep/Banner Demographics</a></li>
        <li><a href="tabs/contact.php">PCC Prep Contact</a></li>
        <li><a href="tabs/banner_contact.php">Banner Contact</a></li>
        <li><a href="tabs/student_other.php">Student ID</a></li>
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