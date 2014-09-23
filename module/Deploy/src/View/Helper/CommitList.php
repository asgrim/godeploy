<?php

namespace Deploy\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CommitList extends AbstractHelper
{
    public function __invoke($commitListData)
    {
        $html = '<table class="table table-bordered table-condensed" style="width: auto;">';

        $html .= '<thead>';
        $html .= '  <tr class="active">';
        $html .= '    <th>Commit</th>';
        $html .= '    <th>Author</th>';
        $html .= '    <th>Message</th>';
        $html .= '  </tr>';
        $html .= '</thead>';

        $html .= '<tbody>';
        if (count($commitListData->commits)) {
            foreach ($commitListData->commits as $commit) {
                $html .= '  <tr>';
                $html .= '    <td>' . substr($commit['HASH'], 0, 8) . '</td>';
                $html .= '    <td>' . $commit['AUTHOR'] . '</td>';
                $html .= '    <td>' . $commit['MESSAGE'] . '</td>';
                $html .= '  </tr>';
            }
        } else {
            $html .= '  <tr>';
            $html .= '    <td colspan="3" class="text-center text-warning">No changes</td>';
            $html .= '  </tr>';
        }
        $html .= '</tbody>';

        $html .= '</table>';

        if ($commitListData->swapped) {
            $html .= '<p class="text-warning bg-warning" style="padding: 15px;">';
            $html .= '  <strong>Note:</strong> this deployment rolls back these commits';
            $html .= '</p>';
        }

        return $html;
    }
}
