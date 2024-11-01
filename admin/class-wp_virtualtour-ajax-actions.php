<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/admin
 * @author     Martin Blaas <info@form-fabrik.de>
 */
class Wp_virtualtour_Ajax_Actions
{

  public function __construct()
  {
    add_action('wp_ajax_general_ajax_action', array($this, 'general_ajax_action'));
  }

  public function general_ajax_action()
  {
    $data = null;
    if (isset($_POST['data'])) {
      // parse json
      $data = json_decode(stripslashes($_POST['data']));
      // bail if no valid json
      if (json_last_error() != JSON_ERROR_NONE) {
        echo json_encode(array('error' => 'Invalid json data.'));
        wp_die();
      }
      // valid json, so sanitize it
      $data = $this->sanitizeData($data);
    }

    $actions = [
      'addNewTour',
      'getTours',
      'deleteTour',
      'updateTour',
      'deleteAll',
      'getSingleTour'
    ];

    if (!in_array($_POST['do'], $actions)) { // bail if no valid action
      echo json_encode(array('error' => 'No registered action.'));
      wp_die();
    }

    if (!$data && $_POST['do'] !== 'getTours') { // getTours is only action without $data
      echo json_encode(array('error' => 'No data found.'));
      wp_die();
    }

    switch ($_POST['do']) {
      case 'addNewTour':
        $this->addNewTour($data);
        wp_die();
      case 'getTours':
        echo json_encode($this->getTours());
        wp_die();
      case 'deleteTour':
        $this->deleteTour($data);
        wp_die();
      case 'updateTour':
        $this->updateTour($data);
        wp_die();
      case 'deleteAll':
        $this->deleteAll();
        wp_die();
      case 'getSingleTour':
        $this->getSingleTour($data);
        wp_die();
    }
    wp_die();
  }

  private function addNewTour($data)
  {
    if (!isset($data->default->title) || !$data->default->title) {
      echo json_encode(array('error' => 'No title set'));
      wp_die();
    }

    $existingTours = $this->getTours();

    $newTourId = uniqid();

    $existingTours[] = array(
      'id' => $newTourId,
      'title' => $data->default->title,
    );

    $singleTour = array(
      'id' => $newTourId,
      'default' => (object)array(
        'title' => $data->default->title,
        'autoLoad' => true
      )
    );

    update_option('wp_virtualtours_tours', $existingTours);
    update_option('wp_virtualtours_tour_' . $newTourId, $singleTour);

    echo json_encode($existingTours);
    wp_die();
  }

  private function getSingleTour($data)
  {
    if (!isset($data->id)) {
      echo json_encode(array('error' => 'No ID specified.'));
      wp_die();
    }

    $tour = get_option('wp_virtualtours_tour_' . $data->id);

    if (!$tour) {
      echo json_encode(array('error' => 'Tour not found.'));
      wp_die();
    }

    echo json_encode($this->sanitizeData((object)$tour));
  }

  private function updateTour($data)
  {
    $existingTours = $this->getTours();
    $key = array_search($data->id, array_column($existingTours, 'id'));

    $existingTours[$key]->title = property_exists($data->default, 'title') ? sanitize_text_field($data->default->title) : '';

    update_option('wp_virtualtours_tour_' . $data->id, $data);
    update_option('wp_virtualtours_tours', $existingTours);

    wp_die();
  }

  private function deleteTour($data)
  {
    if (!isset($data->id)) {
      echo json_encode(array('error' => 'No ID specified.'));
      wp_die();
    }

    $existingTours = $this->getTours();

    $key = array_search($data->id, array_column($existingTours, 'id'));

    unset($existingTours[$key]);
    $existingTours = array_values($existingTours);

    update_option('wp_virtualtours_tours', $existingTours);
    delete_option('wp_virtualtours_tour_' . $data->id);

    echo json_encode($this->getTours());
    wp_die();
  }

  private function getTours(): array
  {
    $existingTours = get_option('wp_virtualtours_tours');
    $sanitizedTours = [];

    if ($existingTours) {
      foreach ($existingTours as $existingTour) {
        $sanitizedTours[] = $this->sanitizeData((object)$existingTour);
      }
    }

    return $sanitizedTours;
  }

  // used for dev only
  private function deleteAll()
  {
    update_option('wp_virtualtours_tours', '');
  }

  private function sanitizeData($data): stdClass
  {
    if (!is_object($data)) {
      echo "Not a valid data object.";
      wp_die();
    }

    $sanitizedData = new stdClass();

    if (isset($data->default))
      $sanitizedData->default = (object)[];

    if (isset($data->id))
      $sanitizedData->id = sanitize_text_field($data->id);
    if (isset($data->title))
      $sanitizedData->title = sanitize_text_field($data->title);
    if (isset($data->default->author))
      $sanitizedData->default->author = sanitize_text_field($data->default->author);
    if (isset($data->default->autoLoad))
      $sanitizedData->default->autoLoad = (bool)($data->default->autoLoad);
    if (isset($data->default->compass))
      $sanitizedData->default->compass = (bool)($data->default->compass);
    if (isset($data->default->firstScene))
      $sanitizedData->default->firstScene = sanitize_text_field($data->default->firstScene);
    if (isset($data->default->title))
      $sanitizedData->default->title = sanitize_text_field($data->default->title);

    if (isset($data->scenes)) {
      $sanitizedData->scenes = (object)[];
      foreach ($data->scenes as $key => $scene) {
        $sanitizedData->scenes->{$key} = (object)[];
        if (isset($scene->cssClass))
          $sanitizedData->scenes->{$key}->cssClass = sanitize_text_field($scene->cssClass);
        if (isset($scene->id))
          $sanitizedData->scenes->{$key}->id = sanitize_text_field($scene->id);
        if (isset($scene->panorama))
          $sanitizedData->scenes->{$key}->panorama = esc_url_raw($scene->panorama);
        if (isset($scene->pitch))
          $sanitizedData->scenes->{$key}->pitch = (float)$scene->pitch;
        if (isset($scene->title))
          $sanitizedData->scenes->{$key}->title = sanitize_text_field($scene->title);
        if (isset($scene->type))
          $sanitizedData->scenes->{$key}->type = sanitize_text_field($scene->type);
        if (isset($scene->yaw))
          $sanitizedData->scenes->{$key}->yaw = (float)$scene->yaw;

        if (isset($scene->hotSpots)) {
          $sanitizedData->scenes->{$key}->hotSpots = [];
          $i = 0;
          foreach ($scene->hotSpots as $hotspot) {
            $sanitizedData->scenes->{$key}->hotSpots[$i] = (object)[];
            if (isset($hotspot->id))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->id = sanitize_text_field($hotspot->id);
            if (isset($hotspot->yaw))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->yaw = (float)($hotspot->yaw);
            if (isset($hotspot->pitch))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->pitch = (float)($hotspot->pitch);
            if (isset($hotspot->targetYaw))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->targetYaw = sanitize_text_field($hotspot->targetYaw);
            if (isset($hotspot->targetPitch))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->targetPitch = sanitize_text_field($hotspot->targetPitch);
            if (isset($hotspot->text))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->text = sanitize_text_field($hotspot->text);
            if (isset($hotspot->type))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->type = sanitize_text_field($hotspot->type);
            if (isset($hotspot->URL))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->URL = esc_url_raw($hotspot->URL);
            if (isset($hotspot->sceneId))
              $sanitizedData->scenes->{$key}->hotSpots[$i]->sceneId = sanitize_text_field($hotspot->sceneId);
            $i++;
          }
        }
      }
    }
    return $sanitizedData;
  }

}
