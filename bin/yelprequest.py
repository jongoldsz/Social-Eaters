#code to create yelp api request
#written for python 2.7
#requires oauth2
#run with argument -h to get help info

import oauth2 as oauth
import time
import urllib
import urllib2
import json
import os
from optparse import OptionParser

consumer_key = 'P4y__XNfF2xc2rHVrYDfQQ'

consumer_secret = 'iVxHHaQYt5ckAcbbGQGfQyl7Igs'

a_token = 'ni8HpQOmOxBZ9g1miW5g17F4qIblfn9p'

a_token_secret = 'JkLTCcNGJ9-BmiDl921qqKT3ARo'

url = "http://api.yelp.com/v2/search?"

def getparser():
    parser = OptionParser()

    parser.add_option('-r', '--results',
                      help='Number of results from api [default: 20, max: 20]',
                      action="store", dest='results', type='int', default=20)
    parser.add_option('-l', '--location',
                      help='City/location to search for restaurants [default: Troy,NY]',
                      action="store", dest='location', type="string",
                      default="Troy,NY")
    parser.add_option('-t', '--term',
                      help='Search term [default: restaurants]',
                      action="store", dest='term', type="string",
                      default="restaurants")
    parser.add_option('-c', '--coordinates',
                      help='Search by latitude and longitude coordinates instead of location (lat/lon must be set if this is selected) [defaut: false]',
                      action="store_true", dest="coordinates", default=False)
    parser.add_option('--lat', '--latitude',
                      help='Set latitude, must be set if coordinate used [defaut: None]',
                      action="store", dest='lat', type="float",
                      default=None)
    parser.add_option('--lon', '--longitude',
                      help='Set longitude, must be set if coordinate used [defaut: None]',
                      action="store", dest='lon', type="float",
                      default=None)
    parser.add_option('-a', '--acc',
                      help='Set lat/long accuracy (optional) [default: None]',
                      action="store", dest='acc', type="float",
                      default=None)
    parser.add_option('-u', '--url',
                      help='return url instead of json [default: False]',
                      action="store_true", dest='url', default=False)
    

    (options, args) = parser.parse_args()
    return parser


#limit = results from api, max is 20
def get_request(parser): 

    token = oauth.Token(key= a_token,
                        secret= a_token_secret)
    consumer = oauth.Consumer(key=consumer_key,
                              secret=consumer_secret)

    

    #location = Troy for testing
    params = {
        'oauth_version': "1.0",
        #'oauth_consumer_key' : consumer_key
        #'oauth_token' : token,
        'oauth_nonce': oauth.generate_nonce(),
        'oauth_timestamp': int(time.time()),
        'term': parser.values.term,
        #'location': location,
        'limit' : parser.values.results
        
    }


    params['oauth_token'] = token.key
    params['oauth_consumer_key'] = consumer.key

    if parser.values.coordinates is False:
        params['location'] = parser.values.location

    if parser.values.coordinates:
        #if lat or lon not initialized return an error
        if parser.values.lat is None or parser.values.lon is None:
            #requested error code
            return 1
        else:
            params['ll'] = str(parser.values.lat)+','+str(parser.values.lon)
        
            if parser.values.acc is not None:
                params['ll'] = params['ll']+','+str(parser.values.acc)
    


    # Create our request. Change method, etc. accordingly.
    req = oauth.Request(method="GET", url=url, parameters=params)

    # Sign the request.
    signature_method = oauth.SignatureMethod_HMAC_SHA1()
    req.sign_request(signature_method, consumer, token)

    #may change to req.to_url() to just return request url
    return req

def get_url(parser):
    req = get_request(parser)
    return req.to_url()

#remember for later: json.get() can give you another json
def get_data(parser):
    req = get_request(parser)
    if req == 1:
	 return 1

    try:
        conn = urllib2.urlopen(req.to_url(), None)
        try:
          response = json.loads(conn.read())
        finally:
          conn.close()
    except urllib2.HTTPError, error:
        response = json.loads(error.read())


    return json.dumps(response, sort_keys=True, indent = 2)
    #return response

    
if __name__ == "__main__":
    parser = getparser()
    if parser.values.url:
        print get_url(parser)
    else:
        print get_data(parser)

