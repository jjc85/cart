#!/bin/sh

set -e

host="$1"
port="$2"
shift 2
cmd="$@"

echo "Waiting for $host:$port to be available..."

until nc -z "$host" "$port"; do
  printf '.'
  sleep 1
done

echo ""
echo "$host:$port is up - executing command"

exec $cmd