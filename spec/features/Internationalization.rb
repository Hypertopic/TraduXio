feature 'Localization' do

    scenario 'french' do
        prefer_language('fr-FR;q=0.9,fr;q=0.8,en;q=0.7')
        visit '/works/'
        click_on 'Ajouter une œuvre'
        expect(page).to have_content 'Œuvre originale'
    end

    scenario 'english' do
        prefer_language('en-au,en-us,en,fr')
        visit '/works/'
        click_on 'Add a work'
        expect(page).to have_content 'Original work'
    end

    scenario 'portugese' do
        prefer_language('pt-br,en-us,en')
        visit '/works/'
        click_on 'Adicionar uma obra'
        expect(page).to have_content 'Obra original'
    end

    scenario 'spanish' do
        prefer_language('es-ar,es-es,en-us,en')
        visit '/works/'
        click_on 'Añadir una obra'
        expect(page).to have_content 'Obra original'
    end

    scenario 'chinese' do
        prefer_language('zh-cn,en-us,en')
        visit '/works/'
        click_on '添加作品'
        expect(page).to have_content '原始作品'
    end

end
