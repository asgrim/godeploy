<?php

namespace Deploy\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CommitList extends AbstractHelper
{
    public function __invoke($commitListData)
    {
        $html = '<table class="table table-bordered table-condensed" style="width: auto;">';

        $html .= '<thead>';
        $html .= '  <tr>';
        $html .= '    <th>Commit</th>';
        $html .= '    <th>Author</th>';
        $html .= '    <th>Message</th>';
        $html .= '  </tr>';
        $html .= '</thead>';

        $html .= '<tbody>';
        foreach ($commitListData->commits as $commit) {
            $html .= '  <tr>';
            $html .= '    <td>' . substr($commit['HASH'], 0, 8) . '</td>';
            $html .= '    <td>' . $commit['AUTHOR'] . '</td>';
            $html .= '    <td>' . $commit['MESSAGE'] . '</td>';
            $html .= '  </tr>';
        }
        $html .= '</tbody>';

        $html .= '</table>';

        return $html;
    }
}
