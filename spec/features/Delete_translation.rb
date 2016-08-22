require 'spec_helper'
require "translation_helper"

feature "Delete translations" do
  given!(:work_metadata) { create_random_work }

  scenario 'Delete one translation' do
    translation=create_random_translation
    delete_translation translation[:author]
    expect(page).not_to have_translation(translation[:author])
  end

  scenario 'Delete second translation' do
    create_random_translation
    translation=create_random_translation
    delete_translation translation[:author]
    expect(page).not_to have_translation(translation[:author])
  end

  scenario 'Delete first translation' do
    translation=create_random_translation
    create_random_translation
    delete_translation translation[:author]
    expect(page).not_to have_translation(translation[:author])
  end

end
