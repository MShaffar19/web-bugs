<?php
$desc = "{$bug['package_name']} {$bug['bug_type']}\nReported by ";
if ($bug['handle']) {
	$desc .= "{$bug['handle']}\n";
} else {
	$desc .= substr($bug['email'], 0, strpos($bug['email'], '@')) . "@...\n";
}
$desc .= date(DATE_ATOM, $bug['ts1a']) . "\n";
$desc .= "PHP: {$bug['php_version']}, OS: {$bug['php_os']}, Package Version: {$bug['package_version']}\n\n";
$desc .= $bug['ldesc'];
$desc = '<pre>' . utf8_encode(htmlspecialchars($desc)) . '</pre>';

$state = 'http://xmlns.com/baetle/#Open';
switch ($bug['status']) {
	case 'Closed':
		$state = 'http://xmlns.com/baetle/#Closed';
		break;
	case 'Wont fix':
		$state = 'http://xmlns.com/baetle/#WontFix';
		break;
	case 'No Feedback':
		$state = 'http://xmlns.com/baetle/#Incomplete';
		break;
	case 'Bogus':
		$state = 'http://xmlns.com/baetle/#WorksForMe';
		break;
	case 'Duplicate':
		$state = 'http://xmlns.com/baetle/#Duplicate';
		break;
	case 'Suspended':
		$state = 'http://xmlns.com/baetle/#Later';
		break;
	case 'Assigned':
		$state = 'http://xmlns.com/baetle/#Started';
		break;
	case 'Open':
		$state = 'http://xmlns.com/baetle/#Open';
		break;
	case 'Analyzed':
	case 'Verified':
		$state = 'http://xmlns.com/baetle/#Verified';
		break;
	case 'Feedback':
		$state = 'http://xmlns.com/baetle/#NotReproducable';
		break;
}

print '<?xml version="1.0"?>';
?>
<rdf:RDF 
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns="http://purl.org/rss/1.0/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:btl="http://xmlns.com/baetle/#"
	xmlns:wf="http://www.w3.org/2005/01/wf/flow#"
	xmlns:sioc="http://rdfs.org/sioc/ns#"
	xmlns:foaf="http://xmlns.com/foaf/0.1/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
	<channel rdf:about="<?php echo $uri; ?>">
		<title><?php echo $bug['package_name']; ?> Bug #<?php echo intval($bug['id']); ?></title>
		<link><?php echo $uri; ?></link>
		<description><?php echo utf8_encode(htmlspecialchars("[{$bug['status']}] {$bug['sdesc']}")); ?></description>

		<dc:language>en-us</dc:language>
		<dc:creator><?php echo $site; ?>-webmaster@lists.php.net</dc:creator>
		<dc:publisher><?php echo $site; ?>-webmaster@lists.php.net</dc:publisher>

		<admin:generatorAgent rdf:resource="http://<?php echo $site_url, $basedir; ?>" />
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>1</sy:updateFrequency>
		<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

		<items>
			<rdf:Seq>
				<rdf:li rdf:resource="<?php echo $uri; ?>" />
<?php foreach ($comments as $comment) { ?>
				<rdf:li rdf:resource="<?php echo $uri; ?>#<?php echo $comment['added']; ?>"/>
<?php } ?>
			</rdf:Seq>
		</items>
	</channel>

	<btl:Bug rdf:about="<?php echo $uri; ?>">
		<btl:summary><?php echo utf8_encode(htmlspecialchars($bug['sdesc'])); ?></btl:summary>
		<btl:description><?php echo utf8_encode(htmlspecialchars($bug['ldesc'])); ?></btl:description>
		<wf:state rdf:resource="<?php echo $state; ?>" />
	</btl:Bug>

	<item rdf:about="<?php echo $uri; ?>">
		<title><?php echo utf8_encode(htmlspecialchars(substr($bug['email'], 0, strpos($bug['email'], '@')))) . "@... [{$bug['ts1']}]"; ?></title>
		<link><?php echo $uri; ?></link>
		<description><![CDATA[<?php echo $desc; ?>]]></description>
		<content:encoded><![CDATA[<?php echo $desc; ?>]]></content:encoded>
		<dc:date><?php echo date(DATE_ATOM, $bug['ts1a']); ?></dc:date>
	</item>

<?php
	foreach ($comments as $comment) {
		if (empty($comment['registered'])) { continue; }

		$ts = urlencode($comment['ts']);
		$displayts = date('Y-m-d H:i', $comment['added'] - date('Z', $comment['added']));

?>
		<item rdf:about="<?php echo $uri; ?>#<?php echo $comment['added']; ?>">
			<title>
<?php
		if ($comment['handle']) {
			echo utf8_encode(htmlspecialchars($comment['handle'])) . " [$displayts]";
		} else {
			echo utf8_encode(htmlspecialchars(substr($comment['email'], 0, strpos($comment['email'], '@')))) . "@... [$displayts]";
		}
?>
			</title>

			<link><?php echo $uri; ?>#<?php echo $comment['added']; ?></link>
			
			<description><![CDATA[<pre><?php echo utf8_encode(htmlspecialchars($comment['comment'])); ?></pre>]]></description>
			<content:encoded><![CDATA[<pre><?php echo utf8_encode(htmlspecialchars($comment['comment'])); ?></pre>]]></content:encoded>
			<dc:date><?php echo date(DATE_ATOM, $comment['added']); ?></dc:date>
		</item>
<?php } ?>

</rdf:RDF>