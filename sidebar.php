<?php
/*
 * Code to draw sidebar, including calendar, controls and credits.
 * 
 */

//TODO: Currently only allows for landscape screens. Allow for portrait screens. Possibly place sidebar as header?

require_once $_SESSION['root_dir'].'/classes/CalendarMonthMotion.php';
$setupURL = $_SESSION['webcam']['server'].":".$_SESSION['webcam']['setup_port'] ;
?>

<div class="sidebar">
    <script type="text/javascript" src="<?php echo $_SESSION['server_dir'].'/js/sidebar.js' ?>"></script>
    <p class=title>
        <?php
        echo gettext("config_title") . "<br /><span style='font-size:smaller;'>version " . gettext("config_version") . "</span>" ;
        ?>
    </p>
    
    <div class="minicalendar">
        <?php
        $cal = new CalendarMonthMotion() ;
        echo $cal->getHTML() ;
        ?>
   </div>
 
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
            <a href="http://<?php echo $setupURL ?>" target=_blank>
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
            <a href='https://github.com/ndpegram/MotionBrowser/' target=_blank>github.com/ndpegram<br />/MotionBrowser/
            </a>
        </p>

        <p class=credits>
            <a href='https://motion-project.github.io/' target=_blank>
                motion-project.github.io/
            </a>
        </p>
    </div>


</div>