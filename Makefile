# Sacfeed Makefile
# - compiles js and css source files

all:
	cli/build.php -v
combine:
	cli/build.php -c -v
clean:
	cli/build.php -r -v
help:
	cli/build.php --help
