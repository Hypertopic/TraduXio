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

## Ruby environment installation on Windows :

Download 'RubyInstaller' from 
http://rubyforge.org/frs/download.php/76955/rubyinstaller-2.0.0-p195.exe.

And install it.

Start Command Prompt with Ruby from
C:\Windows\System32\cmd.exe /E:ON /K C:\Ruby200\bin\setrbvars.bat


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
