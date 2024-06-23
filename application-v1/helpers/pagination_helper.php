<?php


function initializePagination($totalRows, $link, $useQueryString=false)
{
    $ci =& get_instance();
    $ci->load->library('pagination');
    $config['base_url'] = base_url($link);
    $config['total_rows'] = $totalRows;
    $config['per_page'] = getNumberOfItemsPerPage();

    if($useQueryString == true){
        $config['page_query_string'] = true;
    }

    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul><!--pagination-->';

    $config['first_link'] = '&laquo; First';
    $config['first_tag_open'] = '<li class="">';
    $config['first_tag_close'] = '</li>';

    $config['last_link'] = 'Last &raquo;';
    $config['last_tag_open'] = '<li class="">';
    $config['last_tag_close'] = '</li>';

    $config['next_link'] = 'Next &rarr;';
    $config['next_tag_open'] = '<li class="">';
    $config['next_tag_close'] = '</li>';

    $config['prev_link'] = '&larr; Previous';
    $config['prev_tag_open'] = '<li class="">';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li class="">';
    $config['num_tag_close'] = '</li>';
    return $ci->pagination->initialize($config);
}

function getNumberOfItemsPerPage(){
    return RECORDS_PER_PAGE;
}


?>
