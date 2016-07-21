require 'spec_helper'

feature 'Set License' do

  background 'Open work' do
    open_work "Howard Phillips Lovecraft", "The lamp (Fungi from Yuggoth, 6)"
  end

  scenario 'Edit License' do
    open_translation "Aurélien Bénel"
    edit_translation "Aurélien Bénel"
    change_license "Aurélien Bénel"
    expect(page).to have_content "License characteristics"
    orig_license=find("div.license-name").text
    if (orig_license != "by-nc-sa")
      choose "derivatives-sa"
      choose "commercial-nc"
      license="by-nc-sa"
    else
      choose "derivatives-nd"
      choose "commercial-none"
      license="by-nd"
    end
    expect(page).to have_content license
    click_on "save-license"
    open_translation "Aurélien Bénel"
    expect(page).to have_css "div.license img[name='#{license}']"
  end

end
