<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datatables_model extends CI_Model {

    public function json_datatables($getParams = [])
    {
        $tbl        = @$getParams['tbl'];
        $select     = !empty($getParams['select']) ? $getParams['select'] : '*';
        $where      = @$getParams['where'];
        $join       = !empty($getParams['join']) ? $getParams['join'] : [];
        $orderBy    = @$getParams['orderBy'];
        $searchable = @$getParams['search'];
        $groupBy    = @$getParams['groupBy'];
        $encrypt_id = !empty($getParams['encrypt_id']) ? $getParams['encrypt_id'] : false;

        $params = $this->input->get(); // $_REQUEST di CI3 pakai $this->input->get() atau $this->input->post()
        $start = isset($params['start']) ? $params['start'] : 0;
        $length = isset($params['length']) ? $params['length'] : 10;
        $search_value = isset($params['search']['value']) ? $params['search']['value'] : '';

        // Start Query
        $this->db->select($select);
        $this->db->from($tbl);

        // Handle Join
        if (!empty($join)) {
            foreach ($join as $j) {
                $table_join = $j[0];
                $on_condition = $j[1];
                $type = isset($j[2]) ? strtoupper($j[2]) : 'INNER';
                $this->db->join($table_join, $on_condition, $type);
            }
        }

        // Where condition
        if (!empty($where)) {
            $this->db->where($where);
        }

        // Search
        if (!empty($search_value)) {
            if (empty($searchable)) {
                if (!empty($select) && $select != '*') {
                    $select_fields = explode(',', $select);
                    $searchable = [];
                    foreach ($select_fields as $field) {
                        $field = trim(explode(' as ', $field)[0]);
                        $searchable[] = $field;
                    }
                }
            }

            if (!empty($searchable)) {
                $this->db->group_start();
                foreach ($searchable as $index => $field) {
                    if ($index == 0) {
                        $this->db->like($field, $search_value);
                    } else {
                        $this->db->or_like($field, $search_value);
                    }
                }
                $this->db->group_end();
            }
        }

        // Group By
        if (!empty($groupBy)) {
            $this->db->group_by($groupBy);
        }

        // Total Records (tanpa limit)
        $queryTotal = $this->db->get();
        $totalRecords = $queryTotal->num_rows();

        // Apply Ordering
        if (!empty($params['order'][0]['column']) && !empty($params['order'][0]['dir'])) {
            $order_column_index = $params['order'][0]['column'];
            $order_dir = $params['order'][0]['dir'];

            if (!empty($params['columns'][$order_column_index]['data'])) {
                $order_field = $params['columns'][$order_column_index]['data'];
                $order_dir = (strtolower($order_dir) == 'asc') ? 'desc' : 'asc'; // Invers

                if (!empty($orderBy) && @$params['draw'] == 1) {
                    $this->db->order_by($orderBy);
                }
                $this->db->order_by($order_field, $order_dir);
            } else {
                if (!empty($orderBy)) {
                    $this->db->order_by($orderBy);
                }
            }
        } else {
            if (!empty($orderBy)) {
                $this->db->order_by($orderBy);
            }
        }

        // Apply Limit
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        // Query with limit
        $this->db->select($select);
        $this->db->from($tbl);

        if (!empty($join)) {
            foreach ($join as $j) {
                $table_join = $j[0];
                $on_condition = $j[1];
                $type = isset($j[2]) ? strtoupper($j[2]) : 'INNER';
                $this->db->join($table_join, $on_condition, $type);
            }
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($search_value)) {
            if (!empty($searchable)) {
                $this->db->group_start();
                foreach ($searchable as $index => $field) {
                    if ($index == 0) {
                        $this->db->like($field, $search_value);
                    } else {
                        $this->db->or_like($field, $search_value);
                    }
                }
                $this->db->group_end();
            }
        }
        if (!empty($groupBy)) {
            $this->db->group_by($groupBy);
        }
        if (!empty($orderBy)) {
            $this->db->order_by($orderBy);
        }
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = $query->result_array();

        // Encrypt ID if needed
        if ($encrypt_id && !empty($data)) {
            foreach ($data as &$d) {
                if (isset($d['id'])) {
                    $d['id'] = encode($d['id']); // Pastikan function encode() sudah ada di helper
                }
            }
        }

        $json_data = [
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data"            => $data
        ];

        // return $json_data;
        echo json_encode($json_data); exit;
    }

}
