incBOOT_ARGS=${incBOOT_ARGS:-0}; if [ $incBOOT_ARGS -eq 0 ]; then
  export incBOOT_ARGS=1
  eval $(cat .env common.env | awk 'BEGIN{ FS="$" }{ print "export " $1 }')
fi
