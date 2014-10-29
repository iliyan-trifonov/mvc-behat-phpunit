#! /bin/bash

PIDFILE="build/phpserver.pid"
PHPSERVERLOG="build/phpserver.log"
SERVERADDRESS="localhost:8888"
FULLSTARTCOMMAND="php -S $SERVERADDRESS -t src/public router.php"

usage ()
{
    echo -e "\nUsage:\n"
    echo -e "$0 [OPTION]\n"
    echo -e "start          starts the php built in server\n"
    echo -e "stop           stops the php built in server\n"
    echo -e "stopall        stops all processes with the same name as the server command\n"
    echo -e "status         shows if the php built in server is started or not\n"
}

isstarted ()
{
    echo `ps aux | grep -v grep | grep "$FULLSTARTCOMMAND" | wc -l`
}
#by default:
COMMAND="status"

#verbosity=0

if [ "$#" -ne 1 ]; then
    echo -e "\e[0;31mIllegal number of parameters!\e[0m"
    usage
    exit 1
fi

ALLOWEDCOMMANDS=( start stop stopall status )

if [[ " $ALLOWEDCOMMANDS[@] " =~ " $1 " ]]; then
    echo -e "\e[0;31mUnkown command '$1'!\e[0m"
    usage
    exit 1
else
    COMMAND=$1
fi

if [ "$COMMAND" == "start" ]; then
      if [ `isstarted` -eq 1 ]; then
        echo -e "\e[0;31mThe server is already started!\e[0m"
        #exit 1
        exit 0
      fi
      echo "Starting the PHP built-in server on: '$SERVERADDRESS' .."
      #`$FULLSTARTCOMMAND > /dev/null 2>&1 & echo $! > $PIDFILE`
      `$FULLSTARTCOMMAND > $PHPSERVERLOG 2>&1 & echo $! > $PIDFILE`
      if [ $? -eq 0 ]; then
        echo "Ready!"
        #echo "Showing the processes:"
        #ps aux | grep "[p]hp -S"
      else
        echo -e "\e[0;31mFailed!\e[0m";
        exit 1;
      fi
elif [ "$COMMAND" == "stop" ]; then
      if [ `isstarted` -eq 0 ]; then
        echo -e "\e[0;31mThe server is not running!\e[0m"
        #exit 1
        exit 0
      fi
      echo "Stopping the built-in PHP Server.."
      kill -s TERM $(cat $PIDFILE)
      if [ $? -eq 0 ]; then
        rm $PIDFILE
        rm $PHPSERVERLOG
        echo "Ready!"
      else
        echo -e "\e[0;31mFailed!\e[0m";
        exit 1;
      fi
elif [ "$COMMAND" == "stopall" ]; then
      if [ `isstarted` -gt 0 ]; then
        echo -e "\e[0;31mRunning server(s) found!\e[0m"
        rm $PIDFILE
        rm $PHPSERVERLOG
      else
        echo "No running servers found"
        exit 0
      fi
      echo "Showing the processes:"
      ps aux | grep "[p]hp -S"
      echo "Killing the server(s).."
      pkill -f "php -S"
      if [ $? -eq 0 ]; then
        rm $PIDFILE
        rm $PHPSERVERLOG
        echo "Ready!"
      else
        echo -e "\e[0;31mFailed!\e[0m";
        exit 1;
      fi
elif [ "$COMMAND" = "status" ]; then
      if [ `isstarted` -eq 1 ]; then
        echo "The server is running"
      else
        echo "The server is stopped"
      fi
else
    echo -e "\e[0;31mUnkown command '$COMMAND'!\e[0m"
    usage
    exit 1
fi
