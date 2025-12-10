<?php

namespace Sircon\Library\Content;

class Image {

	protected $output = '';

	public function __construct(int $attachment_id, array $params = []) {
		if (get_post_type($attachment_id) !== 'attachment') {
			$this->output = '<figure class="featured-image loading-error"><!-- Dynamic image error: Invalid ID: "' . $attachment_id . '"! --></figure>';
			return;
		}

		$img_formats = self::formats($attachment_id);

		$data_formats = [];
		$format_names = [];
		foreach ($img_formats as $format) {
			$format_name = 'data-' . $format['id'];
			$format_names[] = $format_name;
			$data_formats[$format_name] = [];

			foreach ($format as $k => $v) {
				if ($k === 'id') {
					continue;
				}

				$data_formats[$format_name][] = $v;
			}
		}

		$data_outputs = [];
		foreach ($data_formats as $name => $vals) {
			$data_outputs[] = $name . '="' . implode(';', $vals) . '"';
		}

		//Concatenate a fully informative data-string
		$data_output = implode(' ', $data_outputs);
		$data_output .= ' data-formatnames="' . implode(';', $format_names) . '"';
		$img_alt_txt = self::alt($attachment_id);

		//Captions might be handy
		$img_caption = get_post_field('post_excerpt', $attachment_id);

		$params['content_before']   = empty($params['content_before']) ? '' : $params['content_before'];
		$params['content_after']    = empty($params['content_after']) ? '' : $params['content_after'];

		if (empty($params['classes'])) {
			$params['classes'] = [];
		} elseif (!is_array($params['classes'])) {
			$params['classes'] = [$params['classes']];
		}

		$image_loader = '<figure class="dynamic-image ' . implode(' ', $params['classes']) . '" data-sircon-dynamic="1" ' . $data_output . ' data-alt="' . htmlentities($img_alt_txt) . '" data-caption="' . htmlentities($img_caption) . '" data-content-before="' . htmlentities($params['content_before']) . '" data-content-after="' . htmlentities($params['content_after']) . '"></figure>';

		if (!empty($params['href'])) {
			$image_loader = '<a class="dynamic-image-linked" href="' . $params['href'] . '">' . $image_loader . '</a>';
		}

		$this->output =  $image_loader;
	}

	public function get(): string {
		return $this->output;
	}

	public function print(): void {
		echo $this->output;
	}

	public function __toString() {
		return $this->output;
	}

	/**
	 * Get the dynamic image html for the attachment
	 * @param  int          $attachment_id The ID of the attachment
	 * @param  array        $params  Extra params for the dynamic image. i.e content_before and content_after
	 * @return string       HTML for the image loader
	 */
	public static function dynamic(int $attachment_id, array $params = []): self {
		return new self($attachment_id, $params);
	}

	public static function dynamicFeatured(?int $post_id = null, array $params = []): self {
		if (!$post_id) {
			$post_id = get_the_ID();
		}

		if (!has_post_thumbnail($post_id)) {
			$attachment_id = 0;
		} else {
			$attachment_id = get_post_thumbnail_id($post_id);
		}

		if (empty($params['classes'])) {
			$params['classes'] = ['featured-image'];
		}

		if (!empty($params['linked']) && $params['linked'] === true) {
			$params['href'] = get_permalink($post_id);
		}

		return self::dynamic($attachment_id, $params);
	}

	public static function dynamicFeaturedLinked(?int $post_id = null, array $params = []): self {
		$params['linked'] = true;
		return self::dynamicFeatured($post_id, $params);
	}

	/**
	 * Get all possible sizes for an attachment image
	 * @param  int $attachment_id The post_id of the attachment
	 * @return array              An array of available image sizes with id, url, width. height and aspect ratio
	 */
	public static function formats($attachment_id) {
		if (!$attachment_id) {
			return [];
		}

		global $sircon_image_formats;
		if (empty($sircon_image_formats[$attachment_id])) {
			$available_sizes = wp_get_registered_image_subsizes();
			$baseurl = get_bloginfo('url');
			$urls_already_done = []; //avoid duplicates

			foreach ($available_sizes as $size => $meta) {
				$src = wp_get_attachment_image_url($attachment_id, $size);
				if (in_array($src, $urls_already_done)) {
					continue;
				}

				$sircon_image_formats[$attachment_id][] = [
					'id'    => $size,
					'url'   => $src,
					'w'     => $meta['width'],
					'h'     => $meta['height'],
					'h/w'   => round($meta['height'] / $meta['width'], 3)
				];

				$urls_already_done[] = $src;
			}
		}

		return $sircon_image_formats[$attachment_id];
	}

	/**
	 * Get the alt text for an attachment
	 * @param  int $attachment_id The post_id of the attachment
	 * @return string             The alt text, post_excerpt with fallback to post_title
	 */
	public static function alt($attachment_id) {
		$img_alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', 1);
		if (!$img_alt_text) {
			$post = get_post($attachment_id);
			if ($post->excerpt) {
				$img_alt_text = $post->post_excerpt;
			} else {
				$img_alt_text = $post->post_title;
			}
		}

		return $img_alt_text;
	}
}
