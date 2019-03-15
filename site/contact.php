<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('priv/videodetection.php');
require_once('_template.php');

$db = new Database();
$contact_photos = $db->contact_photos();
$agents = $db->agents();
$photos_lines = utils_array_to_lines($contact_photos, 4);
$config = $db->config();
$data = new Data($db);
$contact_video = $config->contact_video();
$contact_unique_photo = utils_contact_unique_photo();

capture_start();
?>
<div class="contacts pb-5">
    <div class="row align-items-center">
        <?php
        if ($contact_video) {
            $video_info = get_video_codes($contact_video);
            $data->scripts .= $video_info[1];
            ?>
            <div class="col-lg-7" style="position: relative;"><?php echo $video_info[0]; ?></div>
            <?php
        }
        if ($contact_unique_photo) {
            ?>
            <div class="col-lg-3">
                <div class="magnifier">
                    <img id="contact-unique-photo" class="img-fluid" src="<?php echo utils_as_link($contact_unique_photo);?>"/>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="agents col-lg-2">
            <?php foreach($agents as $agent) {
                ?>
                <div class="agent my-4">
                    <div class="name"><?php echo $agent->full_name();?></div>
                    <div class="role"><?php echo $agent->role();?></div>
                    <?php if ($agent->email()) { ?><div class="email"><a target="_blank" href="mailto:<?php echo $agent->email();?>"><?php echo $agent->email();?></a></div><?php } ?>
                </div>
                <?php
            } ?>
            <div class="mt-5">
                <a class="btn btn-dark no-border-radius" href="submission.php">for model submission</a>
            </div>
        </div>
    </div>
</div>
<?php
capture_end($data->content);
if ($contact_unique_photo) {
	$size = getimagesize($contact_unique_photo);
	$width = $size[0];
	$height = $size[1];
	capture_start();
	?>
    <script>
        function magnify(imgID, realWidth, realHeight) {
            const zoom = 1;
            var img, glass, w, h, bw;
            img = document.getElementById(imgID);

            /* Create magnifier glass: */
            glass = document.createElement("DIV");
            glass.setAttribute("class", "img-magnifier-glass");

            /* Insert magnifier glass: */
            img.parentElement.insertBefore(glass, img);

            /* Set background properties for the magnifier glass: */
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            glass.style.backgroundSize = "auto";
            bw = 2;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;

            /* Execute a function when someone moves the magnifier glass over the image: */
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);

            /*and also for touch screens:*/
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);

            function moveMagnifier(e) {
                var pos, x, y;
                /* Prevent any other actions that may occur when moving over the image */
                e.preventDefault();
                /* Get the cursor's x and y positions: */
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                /* Prevent the magnifier glass from being positioned outside the image: */
                if (x > img.width) {
                    x = img.width;
                }
                if (x < 0) {
                    x = 0;
                }
                if (y > img.height) {
                    y = img.height;
                }
                if (y < 0) {
                    y = 0;
                }
                let glassLeft = x - w;
                let glassTop = y - h;
                if (glassLeft < 0) glassLeft = 0;
                if (glassLeft > img.width - 2*w) glassLeft = img.width - 2*w;
                if (glassTop < 0) glassTop = 0;
                if (glassTop > img.height - 2*h) glassTop = img.height - 2*h;
                /* Set the position of the magnifier glass: */
                glass.style.left = glassLeft + "px";
                glass.style.top = glassTop + "px";
                /* Display what the magnifier glass "sees": */
                glass.style.backgroundPosition = "-" + ((x * realWidth / img.width) - w + bw) + "px -" + ((y * realHeight / img.height) - h + bw) + "px";
                console.log(`${x} ${y}`);
            }

            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                /* Get the x and y positions of the image: */
                a = img.getBoundingClientRect();
                /* Calculate the cursor's x and y coordinates, relative to the image: */
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                /* Consider any page scrolling: */
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x: x, y: y};
            }

            function getImgSize(imgSrc) {
                var newImg = new Image();

                newImg.onload = function () {
                    var height = newImg.height;
                    var width = newImg.width;
                    alert('The image size is ' + width + '*' + height);
                }

                newImg.src = imgSrc; // this must be done AFTER setting onload
            }
        }
    </script>
    <script>
        /* Execute the magnify function: */
        magnify("contact-unique-photo", <?php echo $width;?>, <?php echo $height;?>);
        /* Specify the id of the image, and the strength of the magnifier glass: */
    </script>
	<?php
	capture_end($data->scripts);
}

$data->title = 'Contact | DMM';
$data->pagename = 'contact';
echo template($data);
?>

