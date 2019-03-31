<?php
/**
 * @version 0.0.1
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'Y_PROOFREADING' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
/** @var array $items */
?>

<table class="widefat striped">
    <tr>
        <th>対象表記</th>
        <th>言い換え候補文字列</th>
        <th>指摘の詳細情報</th>
    </tr>
	<?php if ( empty( $items ) ): ?>
        <tr>
            <td colspan='3'>アイテムがありません</td>
        </tr>
	<?php else: ?>
		<?php foreach ( $items as $item ): ?>
            <tr>
                <td><?php $instance->h( $item['surface'] ); ?></td>
                <td><?php $instance->h( $item['word'] ); ?></td>
                <td><?php $instance->h( $item['info'] ); ?></td>
            </tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>
