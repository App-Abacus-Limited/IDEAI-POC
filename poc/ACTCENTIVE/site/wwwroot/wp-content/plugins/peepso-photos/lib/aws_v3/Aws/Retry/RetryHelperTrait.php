<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjQ1U4YmljNXRGKzFNcHhoblB4eGE3UitoQWJDWXJJMkRUbUE0RDFvQkFGWjFjdHBHYURsSm83WnJjd3V4MWJJc2ZtU3lDSk9pcDhpMEVQaEplaWpZQmZKUGd6OEdRakdhdnVkd0JxRlF6aTJYOHlhUmE2Q2pFYWhPbnJFQ3VXMFhRYUw3WG9ob2lROEdteDhoRWtuZWVLSUtrdWE5enVPZmhpLytVL1Z0WGg2*/
namespace Aws\Retry;

use Aws\Exception\AwsException;
use Aws\ResultInterface;

trait RetryHelperTrait
{
    private function addRetryHeader($request, $retries, $delayBy)
    {
        return $request->withHeader('aws-sdk-retry', "{$retries}/{$delayBy}");
    }


    private function updateStats($retries, $delay, array &$stats)
    {
        if (!isset($stats['total_retry_delay'])) {
            $stats['total_retry_delay'] = 0;
        }

        $stats['total_retry_delay'] += $delay;
        $stats['retries_attempted'] = $retries;
    }

    private function updateHttpStats($value, array &$stats)
    {
        if (empty($stats['http'])) {
            $stats['http'] = [];
        }

        if ($value instanceof AwsException) {
            $resultStats = $value->getTransferInfo();
            $stats['http'] []= $resultStats;
        } elseif ($value instanceof ResultInterface) {
            $resultStats = isset($value['@metadata']['transferStats']['http'][0])
                ? $value['@metadata']['transferStats']['http'][0]
                : [];
            $stats['http'] []= $resultStats;
        }
    }

    private function bindStatsToReturn($return, array $stats)
    {
        if ($return instanceof ResultInterface) {
            if (!isset($return['@metadata'])) {
                $return['@metadata'] = [];
            }

            $return['@metadata']['transferStats'] = $stats;
        } elseif ($return instanceof AwsException) {
            $return->setTransferInfo($stats);
        }

        return $return;
    }
}
