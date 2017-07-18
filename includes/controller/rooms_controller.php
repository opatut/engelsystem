<?php
use Engelsystem\ShiftsFilter;
use Engelsystem\ShiftsFilterRenderer;

/**
 * Room controllers for managing everything room related.
 */

/**
 * View a room with its shifts.
 *
 * @return array
 */
function room_controller()
{
    global $privileges;

    if (!in_array('view_rooms', $privileges)) {
        redirect(page_link_to());
    }

    $request = request();
    $room = load_room(false);
    if ($room['show'] != 'Y' && !in_array('admin_rooms', $privileges)) {
        redirect(page_link_to());
    }

    $all_shifts = Shifts_by_room($room);
    $days = [];
    foreach ($all_shifts as $shift) {
        $day = date('Y-m-d', $shift['start']);
        if (!in_array($day, $days)) {
            $days[] = $day;
        }
    }

    $shiftsFilter = new ShiftsFilter(
        true,
        [$room['RID']],
        AngelType_ids()
    );
    $selected_day = date('Y-m-d');
    if (!empty($days)) {
        $selected_day = $days[0];
    }
    if ($request->has('shifts_filter_day')) {
        $selected_day = $request->input('shifts_filter_day');
    }
    $shiftsFilter->setStartTime(parse_date('Y-m-d H:i', $selected_day . ' 00:00'));
    $shiftsFilter->setEndTime(parse_date('Y-m-d H:i', $selected_day . ' 23:59'));

    $shiftsFilterRenderer = new ShiftsFilterRenderer($shiftsFilter);
    $shiftsFilterRenderer->enableDaySelection($days);

    $shiftCalendarRenderer = shiftCalendarRendererByShiftFilter($shiftsFilter);

    return [
        $room['Name'],
        Room_view($room, $shiftsFilterRenderer, $shiftCalendarRenderer)
    ];
}

/**
 * Dispatch different room actions.
 *
 * @return array
 */
function rooms_controller()
{
    $request = request();
    $action = $request->input('action');
    if (!$request->has('action')) {
        $action = 'list';
    }

    switch ($action) {
        case 'view':
            return room_controller();
        case 'list':
        default:
            redirect(page_link_to('admin_rooms'));
            break;
    }

    exit;
}

/**
 * @param array $room
 * @return string
 */
function room_link($room)
{
    return page_link_to('rooms') . '&action=view&room_id=' . $room['RID'];
}

/**
 * @param array $room
 * @return string
 */
function room_edit_link($room)
{
    return page_link_to('admin_rooms') . '&show=edit&id=' . $room['RID'];
}

/**
 * Loads room by request param room_id
 *
 * @param bool $onlyVisible
 * @return array
 */
function load_room($onlyVisible = true)
{
    if (!test_request_int('room_id')) {
        redirect(page_link_to());
    }

    $room = Room(request()->input('room_id'), $onlyVisible);
    if ($room == null) {
        redirect(page_link_to());
    }

    return $room;
}
