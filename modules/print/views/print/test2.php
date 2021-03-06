<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page>
        <placeholders>
            <header>
                <div class="head">
                    <img src="http://www.gis3w.it/sites/default/files/logo_gis3w_0.png" />
                    <div>
                        <h1>Amazon Simple Storage service <span>(Version 2006-03-01)</span></h1>
                        <h2 width="40%" float="left">Quick Reference Card</h2>
                    </div>
                    <h4 class="right">
                        Revised: 04/1/2010<br />Page <page-number />
                    </h4>

                </div>
            </header>
        </placeholders>

        <column-layout number-of-columns="3" margin-between-columns="0" class="columns">
            <div class="bordered">
                <h3>Service Operations</h3>
            </div>
            <div class="bordered">
                <strong>GET Service</strong><br />
                Returns a list of all buckets owned by the authenticated request sender.
                <p class="code">
                    GET / HTTP/1.1<br />
                    Host: s3.amazonaws.com<br />
                    Date: <i>date</i><br />zz
                    Authorization: AWS <i>AWSAccessKeyId:signature</i><br />
                </p>
            </div>
            <div class="bordered">
                <h3>Bucket Operations</h3>
            </div>
            <div class="bordered">
                <strong>PUT Bucket</strong><br />
                Creates a new bucket belonging to the account of the authenticated request sender. Optionally, you can specify a EU (Ireland) or US-West (N. California) location constraint.
                <p class="code">
                    PUT / HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i><br />
                    Content-Length: {0 | <i>length</i>}<br />
                    [<i>&lt;CreateBucketConfiguration&gt; &nbsp;&lt;LocationConstraint&gt;EU&lt;/LocationConstraint&gt; &lt;/CreateBucketConfiguration&gt;</i>]
                </p>
            </div>
            <div class="bordered">
                <strong>GET Bucket</strong><br />
                Lists information about the objects in a bucket for a user that has read access to the bucket.
                <p class="code">
                    GET ?prefix=<i>prefix</i>&amp;marker=<i>marker</i>&amp;max-keys=<i>maxkeys</i>&amp;<br />
                    delimiter=<i>delimiter</i> HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i>
                </p>
            </div>
            <div class="bordered">
                <strong>GET Bucket location</strong><br />
                Lists the location constraint of the bucket for the bucketm owner.
                <p class="code">
                    GET /?location HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i><br />
                </p>
            </div>
            <div class="bordered">
                <strong>DELETE Bucket</strong><br />
                Deletes the specified bucket. All objects in the bucket must be deleted before the bucket itself can be deleted.
                <p class="code">
                    DELETE / HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i>
                </p>
            </div>
            <div class="bordered">
                <strong>POST Object</strong><br />
                For more information about POST, refer to the Amazon Simple Storage Service Developer Guide.
            </div>
            <div class="bordered">
                <h3>Object Operations</h3>
            </div>
            <div class="bordered">
                <strong>GET Object</strong><br />
                Gets an object for a user that has read access to the object.
                <p class="code">
                    GET /<i>destinationObject</i> HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS AWSAccessKeyId:signature<br />
                    [Range:bytes=<i>byte_range</i>]<br />
                    [x-amz-metadata-directive: <i>metadata_directive</i>]<br />
                    [x-amz-if-match: <i>etag</i>]<br />
                    [x-amz-if-none-match: <i>etag</i>]<br />
                    [x-amz-if-unmodified-since: <i>time_stamp</i>]<br />
                    [x-amz-if-modified-since: <i>time_stamp</i>]
                </p>
            </div>
            <div class="bordered">
                <strong>PUT Object</strong><br />
                Adds an object to a bucket for a user that has write access to the bucket. A success response indicates the object was successfully stored; if the object already exists, it will be overwritten.
                <p class="code">
                    PUT /destinationObject HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i><br />
                    Content-Length: <i>length</i><br />
                    Content-MD5: <i>md5_digest</i><br />
                    Content-Type: <i>type</i><br />
                    Content-Disposition: <i>object_information</i><br />
                    Content-Encoding: <i>encoding</i><br />
                    Cache-Control: <i>caching</i><br />
                    Expires: <i>expiration</i><br />
                    <i>&lt;request metadata&gt;</i>
                </p>
            </div>
            <div class="bordered">
                <strong>COPY Object</strong><br />
                Copies an object for a user that has write access to the bucket and read access to the object. All headers prefixed with x-amz- must be signed, including x-amz-copy-source.
                <p class="code">
                    PUT /<i>destinationObject</i> HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i><br />
                    x-amz-copy-source: /<i>source_bucket</i>/<i>sourceObject</i><br />
                    [x-amz-metadata-directive: <i>metadata_directive</i>]<br />
                    [x-amz-copy-source-if-match: <i>etag</i>]<br />
                    [x-amz-copy-source-if-none-match: <i>etag</i>]<br />
                    [x-amz-copy-source-if-unmodified-since:<br />
                    <i>time_stamp</i>]<br />
                    [x-amz-copy-source-if-modified-since: <i>time_stamp</i>]<br />
                    <i>&lt;request metadata&gt;</i>
                </p>
            </div>
            <div class="bordered">
                <strong>HEAD Object</strong><br />
                Retrieves information about an object for a user with read access without fetching the object.
                <p class="code">
                    HEAD /<i>destinationObject</i> HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i>
                </p>
            </div>
            <div class="bordered">
                <strong>DELETE Object</strong><br />
                Deletes the specified object. Once deleted, there is no method to restore or undelete an object.
                <p class="code">
                    DELETE / HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    Date: <i>date</i><br />
                    Authorization: AWS <i>AWSAccessKeyId:signature</i>
                </p>
            </div>
            <div class="bordered">
                <strong>POST Object</strong><br />
                Adds an object to a bucket using forms.
                <p class="code">
                    POST / HTTP/1.1<br />
                    Host: <i>destinationBucket</i>.s3.amazonaws.com<br />
                    User-Agent: <i>browser_data</i><br />
                    Accept: <i>file_types</i><br />
                    Accept-Language: <i>locales</i><br />
                    Accept-Encoding: <i>encoding</i><br />
                    Accept-Charset: <i>character_set</i><br />
                    Keep-Alive: <i>300</i><br />
                    Connection: <i>keep-alive</i><br />
                    Content-Type: multipart/form-data; boundary=----------ID<br />
                    Content-Length: <i>length</i><br />
                    <i>&lt;multiform_data&gt;</i><br />
                    Note: For more information about the multiform data, refer to the Amazon Simple Storage Service Developer Guide.
                </p>
            </div>

            <break />
            <div class="bordered">
                <h3>Miscellaneous</h3>
            </div>
            <div class="bordered">
                <strong>Bucket Name Restrictions</strong><br />
                Amazon S3 bucket names must:
                <ul>
                    <li>Only contain lowercase letters, numbers, periods (.), and dashes (-)</li>
                    <li>Start with a number or letter</li>
                    <li>Be between 3 and 63 characters long</li>
                    <li>Not be in an IP address style (e.g., "192.168.5.4")</li>
                    <li>Not end with a dash</li>
                    <li>Not contain dashes next to periods (e.g., "my-.bucket.com" and "my.-bucket" are invalid)</li>
                </ul>
                <b>Note:</b> Although legacy bucket names can be up to 255 characters, include underscores, and end with a dash, they are not recommended.
            </div>
            <div class="bordered">
                <strong>REST Request Signature</strong><nl/>
                Construct a signature by making an RFC2104 HMAC-SHA1 of the following and converting it to Base64.
                <div class="half left">
                    <strong>Item</strong><br />
                    <p class="code small">
                        HTTP-Verb + "\n" + <br />
                        [Content-MD5] + "\n" + <br />
                        [Content-Type] + "\n" + <br />
                        Date + "\n" + <br />
                        [CanonicalizedAmzHeaders +/n] <br />
                        [CanonicalizedResource]
                    </p>
                </div>
                <div class="half right">
                    <strong>Example</strong><br />
                    <p class="code small">
                        GET\n<br />
                        4gJE4saaMU4BqNR0kLY+lw==\n or \n<br />
                        image/jpeg\n or \n<br />
                        Tue, 6 Mar 2007 19:42:41 +0000\n<br />
                        x-amz-acl:public-read\n<br />
                        x-amz-meta-checksum:crc32\n<br />
                        /mybucket/photos/lolcatz.jpg
                    </p>
                </div>
                The content cannot contain whitespaces and \n represents Unicode code point U+000A.
            </div>

            <break />
            <div class="bordered">
                <h3>Versioning</h3>
            </div>
            <div class="bordered">
                <strong>GET Bucket Object versions</strong>
                <p class="code">
                    Lists metadata about all of the versions of objects in a bucket.<br />
                    GET /?<i>versions</i> HTTP/1.1<br />
                    Host: <i>BucketName</i>.s3.amazonaws.com
                </p>

                <strong>GET Bucket versioning</strong>
                <p class="code">
                    Returns the versioning state of a bucket.<br />
                    GET /?<i>versioning</i> HTTP/1.1<br />
                    Host: <i>myBucket</i>.s3.amazonaws.com
                </p>

                <strong>PUT Bucket versioning</strong>
                <p class="code">
                    Sets the versioning state of an existing bucket.<br />
                    PUT /?<i>versioning</i> HTTP/1.1<br />
                    Host: <i>bucket</i>.s3.amazonaws.com<br />
                    <i>&lt;VersioningConfiguration xmlns="http://s3.amazonaws.com/ doc/2006-03-01/"&gt; &lt;Status>Enabled&lt;/Status&gt; &lt;/VersioningConfiguration&gt;</i>
                </p>

                <strong>DELETE Object versionId</strong>
                <p class="code">
                    Removes a specific object version.<br />
                    DELETE /<i>my-third-image.jpg</i>?<br />
                    Host: <i>bucket</i>.s3.amazonaws.com<br />
                    versionId=&lt;<i>versionID</i>&gt;
                </p>

                <strong>GET Object versionId</strong>
                <p class="code">
                    Returns a specific object version.<br />
                    GET /<i>myObject</i>?<i font-style="bold-italic">versionId</i>=&lt;<i>versionID</i>&gt;<br />
                    Host: <i>bucket</i>.s3.amazonaws.com
                </p>

                <strong>HEAD Object versionId</strong>
                <p class="code">
                    Returns metadata of a specific object version.<br />
                    HEAD /<i>myObject</i>?<i font-style="bold-italic">versionId</i>=&lt;<i>versionID</i>><br />
                    Host: <i>bucket</i>.s3.amazonaws.com
                </p>

                <strong>PUT Object copy versionId</strong>
                <p class="code">
                    Copies a specific object version.<br />
                    PUT /<i>my-second-image.jpg</i> HTTP/1.1<br />
                    Host: <i>bucket</i>.s3.amazonaws.com<br />
                    Date: Wed, 28 Oct 2009 22:32:00 GMT<br />
                    x-amz-copy-source: /bucket/myimage.<br />
                    jpg?<i font-style="bold-italic">versionId</i>=&lt;<i>versionID</i>&gt;
                </p>
            </div>

        </column-layout>
    </dynamic-page>
</pdf>