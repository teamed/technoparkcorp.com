module Tpc
  module Tags
    class Menu < Liquid::Tag
      def render(context)
        html = ''
        map(context).each do |key, items|
          html += draw(nil, key, items, context)
        end
        html
      end

      def draw(parent, key, items, context)
        name = parent.nil? ? key : parent + '/' + key
        post = find(name, context)
        html = "<li><a href='/#{post.permalink}' title='#{post['intro']}'>#{post['label']}</a>"
        if items.length > 0 and !context['page']['permalink'].nil? and context['page']['permalink'].start_with?(name)
          html += '<ul>'
          items.each do |k,v|
            html += draw(name, k, v, context)
          end
          html += '</ul>'
        end
        html + "</li>"
      end

      def map(context)
        site = context.registers[:site]
        map = Hash.new
        site.posts.each do |post|
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

Liquid::Template.register_tag('menu', Tpc::Tags::Menu)
