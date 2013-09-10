TraduXio – A participative platform for cultural texts translators
==================================================================

License: [GNU Affero General Public License](http://www.gnu.org/licenses/agpl.html)
Contact: aurelien.benel@utt.fr

Installation requirements
-------------------------

* Git client
* [CouchDB](http://couchdb.apache.org/)
* [Couchapp](https://github.com/jchris/couchapp)

Installation procedure
----------------------

* Create a database named `traduxio` at <http://127.0.0.1:5984/_utils>.
* In any folder:

        git clone git://github.com/benel/TraduXio.git
        couchapp push http://127.0.0.1:5984/traduxio

* The application should be now accessible at <http://127.0.0.1:5984/traduxio/_design/traduxio/_rewrite/>.

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

