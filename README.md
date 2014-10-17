TraduXio – A participative platform for cultural texts translators
==================================================================

License: [GNU Affero General Public License](http://www.gnu.org/licenses/agpl.html)
Contact: aurelien.benel@utt.fr

Installation requirements
-------------------------

* Git client
* [CouchDB](http://couchdb.apache.org/)
* [Couchapp](https://github.com/jchris/couchapp)
* [Node.js](http://nodejs.org)

Installation procedure
----------------------

* Create a database named `traduxio` at <http://127.0.0.1:5984/_utils>.
* In any folder:

        git clone https://github.com/benel/TraduXio.git
        cd TraduXio
        couchapp push --browse couchdb http://127.0.0.1:5984/traduxio

Tests requirements
------------------

* Ruby
* [Install QT](https://github.com/thoughtbot/capybara-webkit/wiki/Installing-Qt-and-compiling-capybara-webkit)

Note: If you're on MacOS X, [change your `PATH`](http://stackoverflow.com/a/14138490/1121345) so that `gem` refers to brew's gem rather than to system's.

Tests installation procedure
---------------------------

* In any folder:

        sudo gem install capybara capybara-webkit rspec

Tests running
-------------

* In the application folder:

        rspec spec/features/*

