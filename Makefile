# Sacfeed Makefile
# - compiles js and css source files

all:
	cli/build.php -v
clean:
	cli/build.php -c -v
help:
	cli/build.php --help
