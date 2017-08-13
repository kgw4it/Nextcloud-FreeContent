<div id="app">
	<div id="app-content">
		<div id="app-content-wrapper">
			<?php foreach($_['entries'] as $entry){ ?>
			  <p><a href="<?php p($urlGenerator->linkToRoute('FreeContent.page.showPublic', [$entry['id']]); ?>" target="_blank"><?php p($entry['title']); ?></a></p>
			<?php
			}
		</div>
	</div>
</div>

