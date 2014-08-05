module Tpc
  module Tags
    class Sitemap < Liquid::Tag
      def render(context)
        html = ''
        map(context).sort.each do |key, items|
          html += "<div class='sitemap'>" + draw(nil, key, items.sort, context) + "</div>"
        end
        html
      end

      def draw(parent, key, items, context)
        page = context['page']
        name = parent.nil? ? key : parent + '/' + key
        post = find(name, context)
        html = "<li><a href='/#{post.permalink}' title='#{post['intro']}'>#{post['label']}</a>"
        if items.length > 0
          html += '<ul>'
          items.each do |k,v|
            html += draw(name, k, v.sort, context)
          end
          html += '</ul>'
        end
        html + "</li>"
      end

      def map(context)
        excludes = ['about/legalnotes', 'about/privacypolicy', 'about/sitemap', 'about/news/.*']
        site = context.registers[:site]
        map = Hash.new
        site.posts.each do |post|
          next if !excludes.select {|re| post.permalink.match re }.empty?
          node = map
          post.permalink.split('/').each do |path|
            if !node.include? path
              node[path] = Hash.new
            end
            node = node[path]
          end
        end
        map
      end

      def find(name, context)
        site = context.registers[:site]
        site.posts.each do |post|
          if post.permalink == name
            return post
          end
        end
        raise "can't find article '#{name}'"
      end
    end
  end
end

Liquid::Template.register_tag('sitemap', Tpc::Tags::Sitemap)
