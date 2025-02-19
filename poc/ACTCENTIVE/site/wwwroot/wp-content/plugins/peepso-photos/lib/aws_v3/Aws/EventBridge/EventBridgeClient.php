<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjQ1U4YmljNXRGKzFNcHhoblB4eGE3UXgwVm1ORFpmOG1KUW41cUpMTlFyVFJUTm9ISGFtamY2SHdKU1E0WWxLZHZ1UkZwQVR6V25pVGVvaXZNVXRPQUtjSnYrK0R1M2VNRzhncVFMN2FuZDNHR0JBMVFKTmkwR2FRWE5GMllyWHp2RTB4NHpaTmdsSlZnTnJBa1ZULzVsc2lub2d5eWpKaUJaaE1GRUY5NmF0*/
namespace Aws\EventBridge;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon EventBridge** service.
 * @method \Aws\Result activateEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise activateEventSourceAsync(array $args = [])
 * @method \Aws\Result cancelReplay(array $args = [])
 * @method \GuzzleHttp\Promise\Promise cancelReplayAsync(array $args = [])
 * @method \Aws\Result createApiDestination(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createApiDestinationAsync(array $args = [])
 * @method \Aws\Result createArchive(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createArchiveAsync(array $args = [])
 * @method \Aws\Result createConnection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createConnectionAsync(array $args = [])
 * @method \Aws\Result createEndpoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createEndpointAsync(array $args = [])
 * @method \Aws\Result createEventBus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createEventBusAsync(array $args = [])
 * @method \Aws\Result createPartnerEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createPartnerEventSourceAsync(array $args = [])
 * @method \Aws\Result deactivateEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deactivateEventSourceAsync(array $args = [])
 * @method \Aws\Result deauthorizeConnection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deauthorizeConnectionAsync(array $args = [])
 * @method \Aws\Result deleteApiDestination(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteApiDestinationAsync(array $args = [])
 * @method \Aws\Result deleteArchive(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteArchiveAsync(array $args = [])
 * @method \Aws\Result deleteConnection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteConnectionAsync(array $args = [])
 * @method \Aws\Result deleteEndpoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteEndpointAsync(array $args = [])
 * @method \Aws\Result deleteEventBus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteEventBusAsync(array $args = [])
 * @method \Aws\Result deletePartnerEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deletePartnerEventSourceAsync(array $args = [])
 * @method \Aws\Result deleteRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteRuleAsync(array $args = [])
 * @method \Aws\Result describeApiDestination(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeApiDestinationAsync(array $args = [])
 * @method \Aws\Result describeArchive(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeArchiveAsync(array $args = [])
 * @method \Aws\Result describeConnection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeConnectionAsync(array $args = [])
 * @method \Aws\Result describeEndpoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEndpointAsync(array $args = [])
 * @method \Aws\Result describeEventBus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEventBusAsync(array $args = [])
 * @method \Aws\Result describeEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeEventSourceAsync(array $args = [])
 * @method \Aws\Result describePartnerEventSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describePartnerEventSourceAsync(array $args = [])
 * @method \Aws\Result describeReplay(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeReplayAsync(array $args = [])
 * @method \Aws\Result describeRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeRuleAsync(array $args = [])
 * @method \Aws\Result disableRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disableRuleAsync(array $args = [])
 * @method \Aws\Result enableRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise enableRuleAsync(array $args = [])
 * @method \Aws\Result listApiDestinations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listApiDestinationsAsync(array $args = [])
 * @method \Aws\Result listArchives(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listArchivesAsync(array $args = [])
 * @method \Aws\Result listConnections(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listConnectionsAsync(array $args = [])
 * @method \Aws\Result listEndpoints(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEndpointsAsync(array $args = [])
 * @method \Aws\Result listEventBuses(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEventBusesAsync(array $args = [])
 * @method \Aws\Result listEventSources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEventSourcesAsync(array $args = [])
 * @method \Aws\Result listPartnerEventSourceAccounts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPartnerEventSourceAccountsAsync(array $args = [])
 * @method \Aws\Result listPartnerEventSources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPartnerEventSourcesAsync(array $args = [])
 * @method \Aws\Result listReplays(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listReplaysAsync(array $args = [])
 * @method \Aws\Result listRuleNamesByTarget(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRuleNamesByTargetAsync(array $args = [])
 * @method \Aws\Result listRules(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRulesAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result listTargetsByRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTargetsByRuleAsync(array $args = [])
 * @method \Aws\Result putEvents(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putEventsAsync(array $args = [])
 * @method \Aws\Result putPartnerEvents(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPartnerEventsAsync(array $args = [])
 * @method \Aws\Result putPermission(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPermissionAsync(array $args = [])
 * @method \Aws\Result putRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putRuleAsync(array $args = [])
 * @method \Aws\Result putTargets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putTargetsAsync(array $args = [])
 * @method \Aws\Result removePermission(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removePermissionAsync(array $args = [])
 * @method \Aws\Result removeTargets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeTargetsAsync(array $args = [])
 * @method \Aws\Result startReplay(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startReplayAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result testEventPattern(array $args = [])
 * @method \GuzzleHttp\Promise\Promise testEventPatternAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateApiDestination(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateApiDestinationAsync(array $args = [])
 * @method \Aws\Result updateArchive(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateArchiveAsync(array $args = [])
 * @method \Aws\Result updateConnection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateConnectionAsync(array $args = [])
 * @method \Aws\Result updateEndpoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateEndpointAsync(array $args = [])
 */
class EventBridgeClient extends AwsClient {
    public function __construct(array $args)
    {
        parent::__construct($args);
        $stack = $this->getHandlerList();
        $isCustomEndpoint = isset($args['endpoint']);
        $stack->appendBuild(
            EventBridgeEndpointMiddleware::wrap(
                $this->getRegion(),
                [
                    'use_fips_endpoint' =>
                        $this->getConfig('use_fips_endpoint')->isUseFipsEndpoint(),
                    'dual_stack' =>
                        $this->getConfig('use_dual_stack_endpoint')->isUseDualStackEndpoint(),
                ],
                $this->getConfig('endpoint_provider'),
                $isCustomEndpoint
            ),
            'eventbridge.endpoint_middleware'
        );
    }
}
