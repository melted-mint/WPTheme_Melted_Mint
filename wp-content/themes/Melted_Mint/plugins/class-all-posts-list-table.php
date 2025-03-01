<?php
if ( ! class_exists('WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class All_Posts_List_Table extends WP_List_Table {

    private $post_types = array( 'blog', 'novel', 'spinoff', 'community' );

    public function __construct() {
        parent::__construct(array(
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ));
    }

    /**
     * 컬럼 정의
     */
    public function get_columns() {
        $columns = array(
            'cb'         => '<input type="checkbox" />', // Bulk Actions 체크박스
            'title'      => 'Title',
            'post_type'  => 'Type',
            'date'       => 'Date',
        );
        return $columns;
    }

    /**
     * 숨김 컬럼 (기본은 없음)
     */
    public function get_hidden_columns() {
        return array(); 
    }

    /**
     * 정렬 가능한 컬럼
     */
    protected function get_sortable_columns() {
        return array(
            'title' => array('title', false),  // array( 'orderby', 'asc/desc 초기값' )
            'date'  => array('date', false),
        );
    }

    /**
     * 기본 컬럼 데이터
     */
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'post_type':
                return esc_html($item->post_type);
            default:
                return ''; // 다른 컬럼은 기본 비워둠
        }
    }

    /**
     * 제목 컬럼 (Edit 링크 등)
     */
    protected function column_title($item) {
        $edit_link   = get_edit_post_link($item->ID);
        $delete_link = get_delete_post_link($item->ID);

        $actions = array();
        if ($edit_link) {
            $actions['edit'] = sprintf('<a href="%s">Edit</a>', esc_url($edit_link));
        }
        if ($delete_link) {
            $actions['delete'] = sprintf('<a href="%s" style="color:red;">Delete</a>', esc_url($delete_link));
        }

        // WP_List_Table의 row_actions() 함수 사용
        return sprintf(
            '<strong><a class="row-title" href="%s">%s</a></strong> %s',
            esc_url($edit_link),
            esc_html($item->post_title),
            $this->row_actions($actions)
        );
    }

    /**
     * 날짜 컬럼
     */
    protected function column_date($item) {
        $date = get_the_date('Y-m-d', $item->ID);
        return esc_html($date);
    }

    /**
     * Bulk Actions 체크박스 컬럼
     */
    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk_ids[]" value="%d" />',
            $item->ID
        );
    }

    /**
     * Bulk Actions 목록
     */
    protected function get_bulk_actions() {
        return array(
            'bulk_delete' => 'Delete',
        );
    }

    /**
     * 데이터 쿼리 & 테이블 설정
     */
    public function prepare_items() {
        // 1) 컬럼/정렬 설정
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        // ★ 이 줄이 중요: 컬럼 정보를 WP_List_Table에 알려줌
        $this->_column_headers = array($columns, $hidden, $sortable);

        // 2) 검색
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        // 3) 정렬
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'date';
        $order   = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

        // 4) 페이지네이션
        $per_page = 10;
        $current_page = $this->get_pagenum();

        $args = array(
            'post_type'      => $this->post_types,
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => $orderby,
            'order'          => $order,
        );

        if ( ! empty($search) ) {
            $args['s'] = $search; // 기본 검색 (제목+본문)
        }

        $query = new WP_Query($args);

        // WP_Query 결과를 items에 저장
        $this->items = $query->posts;

        // 총 개수
        $total_items = $query->found_posts;

        // 페이지네이션 설정
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ));
    }

    /**
     * Bulk Action 처리 (Delete)
     */
    public function process_bulk_action() {
        if ( $this->current_action() === 'bulk_delete' ) {
            $ids = isset($_REQUEST['bulk_ids']) ? (array) $_REQUEST['bulk_ids'] : array();
            foreach ($ids as $id) {
                wp_trash_post($id); // 휴지통 이동
            }
        }
    }
}