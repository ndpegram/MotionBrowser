<?php
/*
 * Code to draw sidebar, including calendar, controls and credits.
 * 
 */
require_once $ini['root_dir'].'/classes/CalendarMonthMotion.php';
?>

<div class="sidebar">
    <p class=title>
        <?php
        echo gettext("config_title") . "<br /><span style='font-size:smaller;'>version " . gettext("config_version") . "</span>" ;
        ?>
    </p>
    
    <?php
    $cal = new CalendarMonthMotion() ;
    echo $cal->getHTML() ;
    ?>

    <div style='text-align: center;'>
        <p class='separator'></p>
        <p>
            <input type=button onclick="javascript:select_all();" value="<?php echo gettext("all") ?>" >
        </p>
        <p>
            <input type=button onclick="javascript:select_none();" value="<?php echo gettext("nothing") ?>">
        </p>
        <p>
            <input type=button onclick="javascript:deleteSelection();" value="<?php echo gettext("erase_selection") ?>">
        </p>

        <p class='separator'></p>
        <p>
            <a href="http://$server_addr:$setup_port" target=_blank>
                <?php echo gettext("config_motion") ?>
            </a>
        </p>
    </div>

    <div class=credits>
        <p class='separator'></p>
        
        <p>
            <?php echo gettext("config_credits") ?>
            <a href="mailto:<?php echo gettext("config_mailname") ?> ">
                <?php echo gettext("config_mailname") ?>
            </a>
        </p>
        <p>
            <a href='https://github.com/ndpegram/MotionBrowser/'>github.com/ndpegram<br />/MotionBrowser/
            </a>
        </p>

        <p class=credits>
            <a href='https://motion-project.github.io/'>
                motion-project.github.io/
            </a>
        </p>
    </div>


</div>