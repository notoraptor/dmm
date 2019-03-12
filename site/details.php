<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data($db);

$data->title = 'Open Call Details | DMM';
$data->pagename = 'details';
capture_start();
?>
<div class="details pb-5 mb-5">
    <h1 class="text-center"><?php echo $config->details_page_title();?></h1>
    <p class="main-text mt-5 text-justify hidden" id="main-text"><?php echo $config->details_page_text();?></p>
	<?php if (utils_submission_bottom_photo()) { ?>
    <div class="image"><img alt="open call details" class="img-fluid" src="<?php echo utils_as_link(utils_submission_bottom_photo());?>"/></div>
	<?php } ?>
    <h2 class="py-5"><?php echo $config->details_page_middle_title();?></h2>
</div>
<?php
capture_end($data->content);
capture_start();
?>
<script>//<!--
    function manageMainText() {
        const element = document.getElementById('main-text');
        if (element) {
            console.log(`width: ${element.clientWidth}`);
            console.log(`height: ${element.clientHeight}`);
            const width = element.clientWidth;
            const height = element.clientHeight;
            const area = width * height;
            const side = Math.sqrt(area);
            element.classList.remove('hidden');
            element.classList.add('displayed');
            element.style.maxWidth = `${side}px`;
        }
    }
    manageMainText();
    //--></script>
<?php
capture_end($data->scripts);
echo template($data);
?>

