# Mise en place de l'environnement de test
## Ruby environment installation on Debian/Ubuntu
### Dependencies installation

```bash
$ sudo apt-get install build-essential
$ sudo apt-get install curl
$ sudo apt-get install zlib1g-dev libreadline-dev libssl-dev libxml2-dev
```

### Ruby installation with RVM (Ruby version manager)

```bash
$ bash -s stable < <(curl -s https://raw.github.com/wayneeseguin/rvm/master/binscripts/rvm-installer)
```

Append the following line to your ~/.bashrc file.

```bash
[[ -s "$HOME/.rvm/scripts/rvm" ]] && . "$HOME/.rvm/scripts/rvm"  # This loads RVM
```

And then reload your bash environment (or close the terminal window and open a new one).

```bash
$ rvm install 1.9.3
$ rvm use 1.9.3
```
Test if everything is okay with Ruby

```bash
$ irb --version
```

### Rubygems installation
RubyGems is a package manager for the Ruby programming language that provides a standard format for distributing Ruby programs and libraries

```bash
$ sudo apt-get install rubygems
```

## Ruby environment installation on Windows

Download 'RubyInstaller' from http://rubyinstaller.org/downloads/.
('Ruby 2.0.0-p195' or 'Ruby 2.0.0-p195 (x64)' depending on your Windows version)

Install 'RubyInstaller'.

Run the file 'setrbvars.bat' in 'bin' repertory of Ruby. (C:\Ruby200\bin\setrvars.bat)

## Rspec installation

```bash
gem install rspec
```

In order to test the installation :

Create one file bowling_spec.rb with the following content :

```bash
# bowling_spec.rb
require_relative 'bowling'

describe Bowling, "#score" do
  it "returns 0 for all gutter game" do
    bowling = Bowling.new
    20.times { bowling.hit(0) }
    bowling.score.should eq(0)
  end
end
```

And another file bowling.rb as well :

```bash
# bowling.rb
class Bowling
  def hit(pins)
  end

  def score
    0
  end
end
```

You can now try the command :

```bash
$ rspec bowling_spec.rb --format nested
```

You should see :

```bash
Finished in 0.00059 seconds
1 example, 0 failures
```

## Capybara webkit installation

```bash
gem install capybara-webkit
```

If you have an error...You have to install Ruby Development Kit.
Then, you need native libraries : Qt, nokogiri (libxml2...), ...

If you have Windows 64 bits, it seems that it's actually impossible to install nokogiri (dependencies problem).
See https://github.com/sparklemotion/nokogiri/issues/864 for more information.

## Ruby Development Kit

Download 'Ruby Development Kit' from http://rubyinstaller.org/downloads/.
('DevKit-mingw64-32-4.7.2-20130224-1151-sfx.exe' or 'DevKit-mingw64-64-4.7.2-20130224-1432-sfx.exe' depending on your Windows version)

Extract the 'Development Kit' and place it into a permanent directory (for example in Ruby directory).