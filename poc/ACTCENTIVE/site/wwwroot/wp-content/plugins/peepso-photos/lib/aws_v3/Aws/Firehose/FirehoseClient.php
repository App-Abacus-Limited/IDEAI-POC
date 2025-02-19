<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjQ1U4YmljNXRGKzFNcHhoblB4eGE3U1hsRWEvU0x5Y0J2ZHB6R1g1ZWkyVUxHUlkyZUppU1N3NnRWV1dhODVRdWxVTjdoN0pOY3BCOUFZclZpZjYwNFVnMVhnd1JxRWFQVkFzY1pSdXgxcjhNbGZVNU4wTFFGdkJtQWk2ekRzYWQ0dlo4bnFjQ0IrMVFQTGNLM2RmcTllbUFiSnh1VytlVU9RM3dLQUJhQXBC*/
namespace Aws\Firehose;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Kinesis Firehose** service.
 *
 * @method \Aws\Result createDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result deleteDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result describeDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result listDeliveryStreams(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listDeliveryStreamsAsync(array $args = [])
 * @method \Aws\Result listTagsForDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result putRecord(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putRecordAsync(array $args = [])
 * @method \Aws\Result putRecordBatch(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putRecordBatchAsync(array $args = [])
 * @method \Aws\Result startDeliveryStreamEncryption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startDeliveryStreamEncryptionAsync(array $args = [])
 * @method \Aws\Result stopDeliveryStreamEncryption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopDeliveryStreamEncryptionAsync(array $args = [])
 * @method \Aws\Result tagDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result untagDeliveryStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagDeliveryStreamAsync(array $args = [])
 * @method \Aws\Result updateDestination(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateDestinationAsync(array $args = [])
 */
class FirehoseClient extends AwsClient {}
