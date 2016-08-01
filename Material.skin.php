<?php
/**
 * Material
 *
 * @file
 * @ingroup Skins
 * @author Samantha Nguyen
 * @author MtmNC
 * @author Jack Phoenix
 * @author George Barnick
 */

// SkinTemplate class
// @ingroup Skins
class SkinMaterial extends SkinTemplate {
	public $skinname = 'material', 
		$stylename = 'Material',
		$template = 'MaterialTemplate', $useHeadElement = true;

	// Add JS via ResourceLoader
 	// @param OutputPage $out
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$out->addModules( 'skins.material.js' );
	}

	// Add CSS via ResourceLoader
	// @param $out OutputPage
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( array(
			'mediawiki.skinning.interface', 
			'skins.material'
		) );
	}
}

// BaseTemplate class
// @ingroup Skins
class MaterialTemplate extends BaseTemplate {
	public function execute() {
		global $wgVersion;
		
		$this->html( 'headelement' );
		?>
		<header id="mw-header" role="group">
			<a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ); ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'mw-logo-link' ) ) ?>> <!-- logo link -->
				<img id="mw-logo-image" role="banner" src="<?php $this->text( 'logopath' ); ?>" alt="<?php $this->text( 'sitename' ) ?>" />
			</a>
			<form class="mw-search" role="search" id="searchform" action="<?php $this->text( 'wgScript' ); ?>">
				<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
				<?php
					echo $this->makeSearchInput( array( 'class' => 'mw-search-input', 'placeholder' => 'Search the Wiki' ) );
					echo $this->makeSearchButton( 'image', array( 'src' => $this->getSkin()->getSkinStylePath( 'images/magnifying-glass.svg' ), 'alt' => 'search button' ) );
				?>
			</form>
			<?php // language links
				if ( $this->data['language_urls'] ) {
					echo '<ul id="mw-language-links" role="menu">';
					foreach ( $this->data['language_urls'] as $key => $langLink ) {
						echo $this->makeListItem( $key, $langLink );
					}
					echo '</ul>';
				}
			?>
			<nav id="nav-user" role="navigation">
				<span class="username-wrapper">
					<?php
						$materialUsername = htmlspecialchars($this->getSkin()->getUser()->getName(), ENT_QUOTES);
						$materialGuest = $this->getSkin()->msg( 'material-guest' )->text();
						if ( class_exists( 'wAvatar' ) && $this->data['loggedin'] ) { //socialProfile:T and logged in:T
							$materialSPAvatar = new wAvatar( $user->getId(), 'l' );
							$materialSPAvatarImg = $materialSPAvatar->getAvatarURL( array( 'height' => ' 40px', 'class' => 'avatar-img' ) );
							?>
							<span class="avatar"><?php echo $materialSPAvatar ?></span>
							<span class="username"><?php echo $materialUsername ?></span>
						<?php
						} elseif ( class_exists( 'wAvatar' ) && !$this->data['loggedin'] ) { //socialProfile:T and logged in:F
							?>
							<span class="avatar"><i class="material-icons">avatar circle</i></span>
							<span class="username"><?php echo $materialGuest ?></span>
						<?php
						} elseif ( !class_exists( 'wAvatar' ) && $this->data['loggedin'] ) { //socialProfile:F and logged in:T
							?>
							<span class="avatar"><i class="material-icons">avatar circle</i></span>
							<span class="username"><?php echo $materialUsername ?></span>
						<?php
						} elseif ( !class_exists( 'wAvatar' ) && !$this->data['loggedin'] ) { //socialProfile:F and logged in:F
							?>
							<span class="avatar"><i class="material-icons">avatar circle</i></span>
							<span class="username"><?php echo $materialGuest ?></span>
						<?php
						}
					?>
				</span>
				<ul>
					<?php
						foreach ( $this->getPersonalTools() as $key => $item ) {
							echo $this->makeListItem( $key, $item );
						}
					?>
				</ul>
			</nav>
		</header>
		<?php //if ( $this->data['newtalk'] ) { ?>
			<div class="new-talk md-tile" role="dialog" aria-live="polite">
				<?php $this->html( 'newtalk' ); ?>
			</div>
		<?php //} ?>
		<?php if ( $this->data['sitenotice'] ) { ?>
			<div id="site-notice" class="md-tile" role="alert" aria-live="polite">
				<?php $this->html( 'sitenotice' ); ?>
			</div>
		<?php } ?>
		<main class="mw-body-content md-tile">
			<?php 
				if ( $this->data['title'] != '' ) { 
			?>
				<section id="title-section">
					<?php // page status indicators
						$oldVersion = version_compare( $wgVersion, '1.25', '<=' );
						if ( $oldVersion ) {
							if ( method_exists( $this, 'getIndicators' ) ) {
								echo $this->getIndicators();
							}
						}
					?>
					<h1 class="first-heading">
						<?php $this->html( 'title' ); ?>
					</h1>
					<?php
						if( isset( $_GET["printable"] ) && trim( $_GET["printable"] ) === 'yes' ){
							?><div id="site-sub"><?php $this->msg( 'tagline' ); ?></div>
						<?php
						}
					?>
					
					<?php 
						if ( $this->data['subtitle'] || $this->data['undelete'] ) { 
					?>
							<h2 id="content-sub">
								<?php $this->html( 'subtitle' ); $this->html( 'undelete' ); ?>
							</h2>
					<?php 
						} 
					?>
					
					<ul class="content-tabs">
						<?php
							foreach ( $this->data['content_navigation'] as $category => $tabs ) {
								foreach ( $tabs as $key => $tab ) {
									echo $this->makeListItem( $key, $tab );
								}
							}
						?>
					</ul>
					
					<ul class="content-tabs">
						<?php
							foreach ( $this->data['content_navigation'] as $category => $tabs ) {
								foreach ( $tabs as $key => $tab ) {
									echo $this->makeListItem( $key, $tab );
								}
							}
						?>
					</ul>
				</section>
			<?php 
				} 
			?>
			
			<?php $this->html( 'bodytext' ) ?>
			<?php
				$this->html( 'catlinks' );
				if ( $this->data['dataAfterContent'] ) {
					$this->html( 'dataAfterContent' );
				}
			?>
		</main>
		<footer id="mw-footer" role="contentinfo">
			<?php foreach ( $this->getFooterLinks() as $category => $links ) { ?>
				<ul id="nav-footer" role="navigation">
					<?php foreach ( $links as $key ) { ?>
						<li>
							<?php $this->html( $key ) ?>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</footer>
		<?php
		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
	}
}
