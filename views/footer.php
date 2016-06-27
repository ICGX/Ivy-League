
	</div>
	<?php if($user->get_permission()->level == 2): ?>
		<div style="position: fixed; bottom: 130px; right: 90px; width:100px; height: 100px;">
			<?php
			$weeklyAward = $user->getWeeklyAward();
			$totalAward = $user->getTotalAward();
			?>
			<p>本週：</p>
			<p><i class="fa fa-star" style="color: yellow; text-shadow: #333 1px 1px" aria-hidden="true"></i><?php if($weeklyAward) printf(" x %s", $weeklyAward); else echo " x 0"; ?></p>
			<p>總數：</p>
			<p><i class="fa fa-star" style="color: yellow; text-shadow: #333 1px 1px" aria-hidden="true"></i><?php if($totalAward) printf(" x %s", $totalAward); else echo " x 0"; ?></p>
		</div>
	<?php endif; ?>
	<div style="background-color:#006755; margin: 0; padding: 0;color:#fff;font-family:Heiti TC; font-size:12px; position: absolute; bottom: 0; width: 100%;">
		<p style="max-width: 1080px; margin: 24px auto">Copyright Ivy League 2016.All Rights Reserved.</p>
	</div>
</body>
</html>
<!-- margin:0px;float:bottom;bottom:0px; left:0px; -->
<!-- style="position:absolute;bottom:0px;color:#fff;font-family:Heiti TC; font-size:12px;padding-bottom:20px;" -->