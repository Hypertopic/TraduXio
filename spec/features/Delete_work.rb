require 'spec_helper'

feature 'Delete a work' do

  scenario 'Delete full work' do
    metadata=create_random_work
    debug metadata
    open_work metadata[:author],metadata[:title]
    delete_full_work
    expect(page).not_to have_content "#{metadata[:title]} – #{metadata[:author]}"
  end

end
