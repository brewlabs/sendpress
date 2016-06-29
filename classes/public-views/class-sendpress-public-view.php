<?php
// SendPress Required Class: SendPress_Public_View
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


// Plugin paths, for including files
if ( ! defined( 'SENDPRESS_PUBLIC_CLASSES' ) )
	define( 'SENDPRESS_PUBLIC_CLASSES', trailingslashit( plugin_dir_path( __FILE__ ) . 'public-views' ) );


// Field classes
class SendPress_Public_View {
	var $_title = 'Manage Subscription';
	var $_data = '';
	var $_visible = true;
	var $_device_type = 'unknown';
	var $_device = '';

	 // List of mobile devices (phones)
    protected $phoneDevices = array(
        'iPhone'        => '(iPhone.*Mobile|iPod|iTunes)',
        'BlackBerry'    => 'BlackBerry|rim[0-9]+',
        'HTC'           => 'HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6425|001HT',
        'Nexus'         => 'Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile',
        'Dell'          => 'Dell.*Streak|Dell.*Aero|Dell.*Venue|DELL.*Venue Pro|Dell Flash|Dell Smoke|Dell Mini 3iX|XCD28|XCD35',
        'Motorola'      => 'Motorola|\bDroid\b.*Build|DROIDX|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT909|XT910|XT912|XT928',
        'Samsung'       => 'Samsung|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|GT-B3410|GT-B3730|GT-B3740|GT-B5510|GT-B5512|GT-B5722|GT-B6520|GT-B7300|GT-B7320|GT-B7330|GT-B7350|GT-B7510|GT-B7722|GT-B7800|GT-C3010|GT-C3011|GT-C3060|GT-C3200|GT-C3212|GT-C3212I|GT-C3222|GT-C3300|GT-C3300K|GT-C3303|GT-C3303K|GT-C3310|GT-C3322|GT-C3330|GT-C3350|GT-C3500|GT-C3510|GT-C3530|GT-C3630|GT-C3780|GT-C5010|GT-C5212|GT-C6620|GT-C6625|GT-C6712|GT-E1050|GT-E1070|GT-E1075|GT-E1080|GT-E1081|GT-E1085|GT-E1087|GT-E1100|GT-E1107|GT-E1110|GT-E1120|GT-E1125|GT-E1130|GT-E1160|GT-E1170|GT-E1175|GT-E1180|GT-E1182|GT-E1200|GT-E1210|GT-E1225|GT-E1230|GT-E1390|GT-E2100|GT-E2120|GT-E2121|GT-E2152|GT-E2220|GT-E2222|GT-E2230|GT-E2232|GT-E2250|GT-E2370|GT-E2550|GT-E2652|GT-E3210|GT-E3213|GT-I5500|GT-I5503|GT-I5700|GT-I5800|GT-I5801|GT-I6410|GT-I6420|GT-I7110|GT-I7410|GT-I7500|GT-I8000|GT-I8150|GT-I8160|GT-I8320|GT-I8330|GT-I8350|GT-I8530|GT-I8700|GT-I8703|GT-I8910|GT-I9000|GT-I9001|GT-I9003|GT-I9010|GT-I9020|GT-I9023|GT-I9070|GT-I9100|GT-I9103|GT-I9220|GT-I9250|GT-I9300|GT-I9300 |GT-M3510|GT-M5650|GT-M7500|GT-M7600|GT-M7603|GT-M8800|GT-M8910|GT-N7000|GT-P6810|GT-P7100|GT-S3110|GT-S3310|GT-S3350|GT-S3353|GT-S3370|GT-S3650|GT-S3653|GT-S3770|GT-S3850|GT-S5210|GT-S5220|GT-S5229|GT-S5230|GT-S5233|GT-S5250|GT-S5253|GT-S5260|GT-S5263|GT-S5270|GT-S5300|GT-S5330|GT-S5350|GT-S5360|GT-S5363|GT-S5369|GT-S5380|GT-S5380D|GT-S5560|GT-S5570|GT-S5600|GT-S5603|GT-S5610|GT-S5620|GT-S5660|GT-S5670|GT-S5690|GT-S5750|GT-S5780|GT-S5830|GT-S5839|GT-S6102|GT-S6500|GT-S7070|GT-S7200|GT-S7220|GT-S7230|GT-S7233|GT-S7250|GT-S7500|GT-S7530|GT-S7550|GT-S8000|GT-S8003|GT-S8500|GT-S8530|GT-S8600|SCH-A310|SCH-A530|SCH-A570|SCH-A610|SCH-A630|SCH-A650|SCH-A790|SCH-A795|SCH-A850|SCH-A870|SCH-A890|SCH-A930|SCH-A950|SCH-A970|SCH-A990|SCH-I100|SCH-I110|SCH-I400|SCH-I405|SCH-I500|SCH-I510|SCH-I515|SCH-I600|SCH-I730|SCH-I760|SCH-I770|SCH-I830|SCH-I910|SCH-I920|SCH-LC11|SCH-N150|SCH-N300|SCH-R100|SCH-R300|SCH-R351|SCH-R400|SCH-R410|SCH-T300|SCH-U310|SCH-U320|SCH-U350|SCH-U360|SCH-U365|SCH-U370|SCH-U380|SCH-U410|SCH-U430|SCH-U450|SCH-U460|SCH-U470|SCH-U490|SCH-U540|SCH-U550|SCH-U620|SCH-U640|SCH-U650|SCH-U660|SCH-U700|SCH-U740|SCH-U750|SCH-U810|SCH-U820|SCH-U900|SCH-U940|SCH-U960|SCS-26UC|SGH-A107|SGH-A117|SGH-A127|SGH-A137|SGH-A157|SGH-A167|SGH-A177|SGH-A187|SGH-A197|SGH-A227|SGH-A237|SGH-A257|SGH-A437|SGH-A517|SGH-A597|SGH-A637|SGH-A657|SGH-A667|SGH-A687|SGH-A697|SGH-A707|SGH-A717|SGH-A727|SGH-A737|SGH-A747|SGH-A767|SGH-A777|SGH-A797|SGH-A817|SGH-A827|SGH-A837|SGH-A847|SGH-A867|SGH-A877|SGH-A887|SGH-A897|SGH-A927|SGH-B100|SGH-B130|SGH-B200|SGH-B220|SGH-C100|SGH-C110|SGH-C120|SGH-C130|SGH-C140|SGH-C160|SGH-C170|SGH-C180|SGH-C200|SGH-C207|SGH-C210|SGH-C225|SGH-C230|SGH-C417|SGH-C450|SGH-D307|SGH-D347|SGH-D357|SGH-D407|SGH-D415|SGH-D780|SGH-D807|SGH-D980|SGH-E105|SGH-E200|SGH-E315|SGH-E316|SGH-E317|SGH-E335|SGH-E590|SGH-E635|SGH-E715|SGH-E890|SGH-F300|SGH-F480|SGH-I200|SGH-I300|SGH-I320|SGH-I550|SGH-I577|SGH-I600|SGH-I607|SGH-I617|SGH-I627|SGH-I637|SGH-I677|SGH-I700|SGH-I717|SGH-I727|SGH-I777|SGH-I780|SGH-I827|SGH-I847|SGH-I857|SGH-I896|SGH-I897|SGH-I900|SGH-I907|SGH-I917|SGH-I927|SGH-I937|SGH-I997|SGH-J150|SGH-J200|SGH-L170|SGH-L700|SGH-M110|SGH-M150|SGH-M200|SGH-N105|SGH-N500|SGH-N600|SGH-N620|SGH-N625|SGH-N700|SGH-N710|SGH-P107|SGH-P207|SGH-P300|SGH-P310|SGH-P520|SGH-P735|SGH-P777|SGH-Q105|SGH-R210|SGH-R220|SGH-R225|SGH-S105|SGH-S307|SGH-T109|SGH-T119|SGH-T139|SGH-T209|SGH-T219|SGH-T229|SGH-T239|SGH-T249|SGH-T259|SGH-T309|SGH-T319|SGH-T329|SGH-T339|SGH-T349|SGH-T359|SGH-T369|SGH-T379|SGH-T409|SGH-T429|SGH-T439|SGH-T459|SGH-T469|SGH-T479|SGH-T499|SGH-T509|SGH-T519|SGH-T539|SGH-T559|SGH-T589|SGH-T609|SGH-T619|SGH-T629|SGH-T639|SGH-T659|SGH-T669|SGH-T679|SGH-T709|SGH-T719|SGH-T729|SGH-T739|SGH-T746|SGH-T749|SGH-T759|SGH-T769|SGH-T809|SGH-T819|SGH-T839|SGH-T919|SGH-T929|SGH-T939|SGH-T959|SGH-T989|SGH-U100|SGH-U200|SGH-U800|SGH-V205|SGH-V206|SGH-X100|SGH-X105|SGH-X120|SGH-X140|SGH-X426|SGH-X427|SGH-X475|SGH-X495|SGH-X497|SGH-X507|SGH-X600|SGH-X610|SGH-X620|SGH-X630|SGH-X700|SGH-X820|SGH-X890|SGH-Z130|SGH-Z150|SGH-Z170|SGH-ZX10|SGH-ZX20|SHW-M110|SPH-A120|SPH-A400|SPH-A420|SPH-A460|SPH-A500|SPH-A560|SPH-A600|SPH-A620|SPH-A660|SPH-A700|SPH-A740|SPH-A760|SPH-A790|SPH-A800|SPH-A820|SPH-A840|SPH-A880|SPH-A900|SPH-A940|SPH-A960|SPH-D600|SPH-D700|SPH-D710|SPH-D720|SPH-I300|SPH-I325|SPH-I330|SPH-I350|SPH-I500|SPH-I600|SPH-I700|SPH-L700|SPH-M100|SPH-M220|SPH-M240|SPH-M300|SPH-M305|SPH-M320|SPH-M330|SPH-M350|SPH-M360|SPH-M370|SPH-M380|SPH-M510|SPH-M540|SPH-M550|SPH-M560|SPH-M570|SPH-M580|SPH-M610|SPH-M620|SPH-M630|SPH-M800|SPH-M810|SPH-M850|SPH-M900|SPH-M910|SPH-M920|SPH-M930|SPH-N100|SPH-N200|SPH-N240|SPH-N300|SPH-N400|SPH-Z400|SWC-E100',
        'Sony'          => 'E10i|SonyEricsson|SonyEricssonLT15iv',
        'Asus'          => 'Asus.*Galaxy',
        'Palm'          => 'PalmSource|Palm', // avantgo|blazer|elaine|hiptop|plucker|xiino ; @todo - complete the regex.
        'Vertu'         => 'Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature', // Just for fun ;)
        'GenericPhone'  => 'PDA;|PPC;|SAGEM|mmp|pocket|psp|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|wap|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser|LG-P500'
    );
    // List of tablet devices.
    protected $tabletDevices = array(
        'BlackBerryTablet'  => 'PlayBook|RIM Tablet',
        'iPad'              => 'iPad|iPad.*Mobile', // @todo: check for mobile friendly emails topic.
        'NexusTablet'       => '^.*Android.*Nexus(?:(?!Mobile).)*$',
        // @reference: http://www.labnol.org/software/kindle-user-agent-string/20378/
        'Kindle'            => 'Kindle|Silk.*Accelerated',
        'SamsungTablet'     => 'SAMSUNG.*Tablet|Galaxy.*Tab|GT-P1000|GT-P1010|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P1000|GT-P3100|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7300|GT-P7320|GT-P7500|GT-P7510|GT-P7511',
        'HTCtablet'         => 'HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200',
        'MotorolaTablet'    => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
        'AsusTablet'        => 'Transformer|TF101',
        'NookTablet'        => 'Android.*Nook|NookColor|nook browser|BNTV250A|LogicPD Zoom2',
        'AcerTablet'        => 'Android.*\b(A100|A101|A200|A500|A501|A510|W500|W500P|W501|W501P)\b',
        'YarvikTablet'      => 'Android.*(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468)',
        'MedionTablet'      => 'Android.*\bOYO\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB',
        'ArnovaTablet'      => 'AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT',
        // @reference: http://wiki.archosfans.com/index.php?title=Main_Page
        'ArchosTablet'      => 'Android.*ARCHOS|101G9|80G9',
        // @reference: http://en.wikipedia.org/wiki/NOVO7
        'AinolTablet'       => 'NOVO7|Novo7Aurora|Novo7Basic|NOVO7PALADIN',
        // @todo: inspect http://esupport.sony.com/US/p/select-system.pl?DIRECTOR=DRIVER
        'SonyTablet'        => 'Sony Tablet|Sony Tablet S',
        'GenericTablet'     => 'Tablet(?!.*PC)|ViewPad7|LG-V909|MID7015|BNTV250A|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|hp-tablet',
    );

	


	function __construct( $title='' ) {

		
		
		$this->title( $title );

		if ( $this->init() === false ) {
			$this->set_visible( false );
			return;
		}
		
	}

	static function _public_css(){ ?>
	<style>
		.subscriber-info{min-height:20px;padding:19px;margin-bottom:20px;background-color:#f5f5f5;border:1px solid #e3e3e3;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05);}.well blockquote{border-color:#ddd;border-color:rgba(0, 0, 0, 0.15);}
		.sendpress-content table{max-width:100%;background-color:transparent;border-collapse:collapse;border-spacing:0;}
.sendpress-content .table{width:100%;margin-bottom:20px;}.sendpress-content .table th,.sendpress-content .table td{padding:8px;line-height:20px;text-align:left;vertical-align:top;border-top:1px solid #dddddd;}
.sendpress-content .table th{font-weight:bold;}
.sendpress-content .table thead th{vertical-align:bottom;}
.sendpress-content .table caption+thead tr:first-child th,.sendpress-content .table caption+thead tr:first-child td,.sendpress-content .table colgroup+thead tr:first-child th,.sendpress-content .table colgroup+thead tr:first-child td,.sendpress-content .table thead:first-child tr:first-child th,.sendpress-content .table thead:first-child tr:first-child td{border-top:0;}
.sendpress-content .table tbody+tbody{border-top:2px solid #dddddd;}
.sendpress-content .table-condensed th,.sendpress-content .table-condensed td{padding:4px 5px;}
.sendpress-content .table-bordered{border:1px solid #dddddd;border-collapse:separate;*border-collapse:collapse;border-left:0;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;}.sendpress-content .table-bordered th,.sendpress-content .table-bordered td{border-left:1px solid #dddddd;}
.sendpress-content .table-bordered caption+thead tr:first-child th,.sendpress-content .table-bordered caption+tbody tr:first-child th,.sendpress-content .table-bordered caption+tbody tr:first-child td,.sendpress-content .table-bordered colgroup+thead tr:first-child th,.sendpress-content .table-bordered colgroup+tbody tr:first-child th,.sendpress-content .table-bordered colgroup+tbody tr:first-child td,.sendpress-content .table-bordered thead:first-child tr:first-child th,.sendpress-content .table-bordered tbody:first-child tr:first-child th,.sendpress-content .table-bordered tbody:first-child tr:first-child td{border-top:0;}
.sendpress-content .table-bordered thead:first-child tr:first-child th:first-child,.sendpress-content .table-bordered tbody:first-child tr:first-child td:first-child{-webkit-border-top-left-radius:4px;border-top-left-radius:4px;-moz-border-radius-topleft:4px;}
.sendpress-content .table-bordered thead:first-child tr:first-child th:last-child,.sendpress-content .table-bordered tbody:first-child tr:first-child td:last-child{-webkit-border-top-right-radius:4px;border-top-right-radius:4px;-moz-border-radius-topright:4px;}
.sendpress-content .table-bordered thead:last-child tr:last-child th:first-child,.sendpress-content .table-bordered tbody:last-child tr:last-child td:first-child,.sendpress-content .table-bordered tfoot:last-child tr:last-child td:first-child{-webkit-border-radius:0 0 0 4px;-moz-border-radius:0 0 0 4px;border-radius:0 0 0 4px;-webkit-border-bottom-left-radius:4px;border-bottom-left-radius:4px;-moz-border-radius-bottomleft:4px;}
.sendpress-content .table-bordered thead:last-child tr:last-child th:last-child,.sendpress-content .table-bordered tbody:last-child tr:last-child td:last-child,.sendpress-content .table-bordered tfoot:last-child tr:last-child td:last-child{-webkit-border-bottom-right-radius:4px;border-bottom-right-radius:4px;-moz-border-radius-bottomright:4px;}
.sendpress-content .table-bordered caption+thead tr:first-child th:first-child,.sendpress-content .table-bordered caption+tbody tr:first-child td:first-child,.sendpress-content .table-bordered colgroup+thead tr:first-child th:first-child,.sendpress-content .table-bordered colgroup+tbody tr:first-child td:first-child{-webkit-border-top-left-radius:4px;border-top-left-radius:4px;-moz-border-radius-topleft:4px;}
.sendpress-content .table-bordered caption+thead tr:first-child th:last-child,.sendpress-content .table-bordered caption+tbody tr:first-child td:last-child,.sendpress-content .table-bordered colgroup+thead tr:first-child th:last-child,.sendpress-content .table-bordered colgroup+tbody tr:first-child td:last-child{-webkit-border-top-right-radius:4px;border-top-right-radius:4px;-moz-border-radius-topleft:4px;}
.sendpress-content .table-striped tbody tr:nth-child(odd) td,.sendpress-content .table-striped tbody tr:nth-child(odd) th{background-color:#f9f9f9;}
.sendpress-content .table-hover tbody tr:hover td,.sendpress-content .table-hover tbody tr:hover th{background-color:#f5f5f5;}
.sendpress-content table [class*=span],.sendpress-content .row-fluid table [class*=span]{display:table-cell;float:none;margin-left:0;}

.sendpress-content .alert{padding:8px 35px 8px 14px;margin-bottom:20px;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);background-color:#fcf8e3;border:1px solid #fbeed5;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;color:#c09853;}
.sendpress-content .alert h4{margin:0;}
.sendpress-content .alert .close{position:relative;top:-2px;right:-21px;line-height:20px;}
.sendpress-content .alert-success{background-color:#dff0d8;border-color:#d6e9c6;color:#468847;}
.sendpress-content .alert-danger,.sendpress-content .alert-error{background-color:#f2dede;border-color:#eed3d7;color:#b94a48;}
.sendpress-content .alert-info{background-color:#d9edf7;border-color:#bce8f1;color:#3a87ad;}
.sendpress-content .alert-block{padding-top:14px;padding-bottom:14px;}
.sendpress-content .alert-block>p,.sendpress-content .alert-block>ul{margin-bottom:0;}
.sendpress-content .alert-block p+p{margin-top:5px;}
	</style>
	<?php
	}

	static function redirect( $link ){
		
		if ( headers_sent() ) {
			echo "<script>document.location.href='" . esc_url_raw( $link ) . "';</script>"; 
		}
		else {
			wp_redirect(  esc_url_raw( $link ) ); 
		}
		exit;
	}

	static function _public_before(){
		$theme = wp_get_theme();
		if( isset($theme->Parent ) ){
			$theme = $theme->Parent;
		} else {
			$theme  = $theme->Template;	

		}
	
		switch(strtolower($theme)){
			case 'twentytwelve':
			case 'twentyeleven':
			
			?>
			<div id="primary" class="site-content">
				<div id="content" role="main">
			<?php
			break;
			case 'genesis':
			do_action( 'genesis_before_content_sidebar_wrap' );
			?>
			<div id="content-sidebar-wrap">
				<?php do_action( 'genesis_before_content' ); ?>
					<div id="content" class="hfeed">
			<?php
			break;

			case 'responsive': ?>
			  <div id="content-full" class="grid col-940">


			<?php
			break;

			case 'twentyten': ?>
				<div id="container">
			<div id="content" role="main">

			<?php


		}
		?>
		<div id="sendpress-public" class="sendpress-content type-page status-publish hentry entry">
			<?php
			do_action('sendpress_public_css');

	}

	static function _public_after(){ ?>
		</div>
	<?php
		$theme = wp_get_theme();
		if( isset($theme->Parent ) ){
			$theme = $theme->Parent;
		} else {
			$theme  = $theme->Template;	

		}	
		switch(strtolower($theme)){
			case 'twentytwelve':
			case 'twentyeleven':
			
			?>
			</div>
		</div>
			<?php
			break;
			case 'genesis': 
			?>
					</div><!-- end #content -->
				<?php do_action( 'genesis_after_content' ); ?>
			</div><!-- end #content-sidebar-wrap -->
			<?php
			do_action( 'genesis_after_content_sidebar_wrap' );
			break;

			case 'responsive': ?>
			  </div>


			<?php
			break;

			case 'twentyten': ?>
				</div>
			</div>

			<?php

		}

	}


	/**
	 * Initializes the view.
	 */
	function init() {
		//Disable W3 Total Cache on Public Pages
		define('DONOTCACHEOBJECT',true);
		define('DONOTCACHEPAGE',true);
		define('DONOTCACHEDB',true);
		add_action('genesis_site_layout','__genesis_return_full_width_content');
		add_action('sendpress_public_before', array('SendPress_Public_View','_public_before'));
		add_action('sendpress_public_after', array('SendPress_Public_View','_public_after'));
		add_action('sendpress_public_css', array('SendPress_Public_View','_public_css'));

		$detect = new SendPress_Mobile_Detect;
    	$this->_device_type = ( $detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    	
    	switch($this->_device_type){
    		case 'phone':
    			foreach ($this->phoneDevices as $key => $value) {
    				if($detect->is($key) ){
    					$this->_device = $key;
    				}
    			}
    		break;
    		case 'tablet';
    			foreach ($this->tabletDevices as $key => $value) {
    				if($detect->is($key) ){
    					$this->_device = $key;
    				}
    			}
    		break;
    		default:
    			

    	}
    	$this->startup();

	}
	function startup(){}

	function page_start(){
		
		$try_theme = SendPress_Option::use_theme_style();
		//moved this do action outside the if because i need it, and now it matches the after.
		do_action('sendpress_public_before_pro');
		if($try_theme){
			get_header();
			do_action('sendpress_public_before');
		} else {


		?><!DOCTYPE html>
<html>
  <head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <title><?php echo $this->title(); ?></title>
	        <link rel='stylesheet' id='sendpress_bootstrap_css-css'  href='<?php echo SENDPRESS_URL; ?>css/public.0.8.7.bootstrap.min.css?ver=0.8.7' type='text/css' media='all' />    
  				<style type="text/css">
  					body{
  						background-color: whiteSmoke;
  						padding-top: 35px;
  					}
  					.container{
  						background: white;
  					}
  					.area{
  						padding: 20px;
  						border: solid 1px #cdcdcd;
  					}
  				</style>
  			


  </head>
  <body>
   	<div class="container">
   		<div class="row">
			<!-- Page Generated by WordPress using SendPress -->
			<div class="span12">
				<div class='area'>
	 	<?php 
	 	}


  				$spdata = array(
  					"ajaxurl"=>  admin_url( 'admin-ajax.php' ),
  					"nonce" => wp_create_nonce( SendPress_Ajax_Loader::$ajax_nonce )
  				);
  				?>
  			<script>var spdata = <?php echo json_encode($spdata); ?></script>
  			<?php
	}

	function page_end(){
		$try_theme = SendPress_Option::use_theme_style();
		do_action('sendpress_public_after');
		if($try_theme){
			do_action('sendpress_public_view_scripts');
			get_footer();

		} else {
		?>
		</div>
			</div>
	</div>
		</div>
		     <script src="<?php echo SENDPRESS_URL; ?>js/jquery-1.9.1.min.js"></script>
  			 <script type='text/javascript' src='<?php echo SENDPRESS_URL; ?>bootstrap/js/bootstrap.js?ver=3.3.2'></script>
	   		<?php do_action('sendpress_public_view_scripts'); ?>
	    </body>
	</html>
	<?php
		}
	}


	function prerender() {}

	/**
	 * Renders the view.
	 */
	function render() {
		$this->page_start();
		$this->html();
		$this->page_end();
	}

	/*
	* Page HTML
	*/

	function html(){
		?>
		<h1>Whoops</h1>
		<p>We couldn't find the page you are looking for.</p>
		Back to our <a href="<?php echo site_url(); ?>">site</a>
	<?php
	}

	function is_visible() {
		return $this->_visible;
	}

	function set_visible( $visible ) {
		$this->_visible = $visible;
	}

	function title( $title=NULL ) {
		if ( ! isset( $title ) )
			return $this->_title;
		$this->_title = $title;
	}
	function data( $data=NULL ) {
		if ( ! isset( $data ) )
			return $this->_data;
		$this->_data = $data;
	}

}



function sp_public_sort($a,$b){
    return strlen($a)-strlen($b);
}
/*
require_once ( SENDPRESS_PUBLIC_CLASSES. '/class-sendpress-public-view-confirm.php' );
require_once ( SENDPRESS_PUBLIC_CLASSES. '/class-sendpress-public-view-link.php' );
require_once ( SENDPRESS_PUBLIC_CLASSES. '/class-sendpress-public-view-manage.php' );
require_once ( SENDPRESS_PUBLIC_CLASSES. '/class-sendpress-public-view-open.php' );
*/
do_action('sendpress_public_view_class_loaded');

